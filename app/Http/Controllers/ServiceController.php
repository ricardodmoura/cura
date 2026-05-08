<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Service;
use App\Models\ServiceDismissal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;

class ServiceController extends Controller
{
    private $catalog = [
        'higiene' => ['label' => 'Higiene', 'price' => 25.00],
        'alimentacao' => ['label' => 'Alimentação', 'price' => 45.00],
        'penso' => ['label' => 'Penso', 'price' => 50.00],
        'injetaveis' => ['label' => 'Injetáveis', 'price' => 80.00],
        'consulta' => ['label' => 'Consulta Médica', 'price' => 100.00]
    ];
    
    /**
     * MARKETPLACE: Lists pending services available for professionals to accept.
     */
    public function index(Request $request)
    {
        // 1. QUERY MARKETPLACE (Serviços disponíveis para aceitar)
        $marketplaceQuery = Service::where('status', 'pending')
                        ->whereNull('professional_id')
                        ->where('patient_id', '!=', Auth::id())
                        ->whereDoesntHave('dismissals', fn ($q) => $q->where('user_id', Auth::id()))
                        ->with('patient');

        if ($request->filled('filter')) {
             $marketplaceQuery->where('service_type', 'like', '%' . $request->filter . '%');
        }

        $availableServices = $marketplaceQuery->orderBy('date', 'asc')->get();


        // 2. QUERY MEUS SERVIÇOS (Serviços que eu já aceitei)
        $myUpcomingServices = Service::where('professional_id', Auth::id())
                        ->whereIn('status', ['confirmed', 'accepted'])
                        ->whereDate('date', '>=', now()->toDateString()) // Apenas de hoje para a frente
                        ->orderBy('date', 'asc')
                        ->orderBy('time', 'asc')
                        ->take(3) // Limite de 3 como pediste
                        ->with('patient')
                        ->get();

        // Passamos as duas listas para a view
        return view('app.service.index', [
            'services' => $availableServices, // Pool / Marketplace
            'myUpcomingServices' => $myUpcomingServices // Meus Aceites
        ]);
    }

    /**
     * ACTION: Professional accepts a service.
     */
    public function accept(Service $service)
    {
        // Apenas profissionais podem aceitar serviços do marketplace.
        abort_unless(Auth::user()->isProfessional(), 403);

        // Profissional precisa de pelo menos uma qualificação verificada.
        $hasVerified = Auth::user()->qualifications()
            ->where('verification_status', \App\Models\Qualification::STATUS_VERIFIED)
            ->exists();
        if (!$hasVerified) {
            return back()->with('error', 'A sua cédula ainda não foi verificada. Não pode aceitar serviços até a equipa Cura concluir a verificação.');
        }

        // 1. Verify if the service is still available
        if ($service->professional_id !== null) {
            return back()->with('error', 'Este serviço já foi aceite por outro profissional.');
        }

        // 2. Assign the service to the logged-in user (Professional)
        $service->update([
            'professional_id' => Auth::id(),
            'status' => 'confirmed' // Change status to confirmed/active
        ]);

        Log::record('service.accept', "Service #{$service->id}");

        return redirect()->route('app.service.index')->with('success', 'Serviço aceite com sucesso! Pode vê-lo na sua agenda.');
    }

    /**
     * Profissional dispensa o serviço — fica oculto do seu pool, sem afetar outros.
     */
    public function dismiss(Service $service)
    {
        abort_unless(Auth::user()->isProfessional(), 403);

        if ($service->professional_id !== null) {
            return back()->with('error', 'Este serviço já foi atribuído.');
        }

        ServiceDismissal::firstOrCreate([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
        ]);

        Log::record('service.dismiss', "Service #{$service->id}");

        return back()->with('success', 'Serviço dispensado.');
    }

    /**
     * Profissional atribuído marca o serviço como concluído.
     */
    public function markCompleted(Service $service)
    {
        abort_unless($service->professional_id === Auth::id(), 403);

        if (!in_array($service->status, ['confirmed', 'accepted', 'in_progress'])) {
            return back()->with('error', 'Este serviço não pode ser marcado como concluído.');
        }

        // Não pode ser concluído antes da hora marcada — só após a prestação.
        if ($service->dateTime->isFuture()) {
            return back()->with('error', 'Só é possível marcar como concluído após a data/hora do serviço.');
        }

        $service->update(['status' => 'completed']);

        // Cria stubs (rating=null) para ambas as partes — aparecem como "Por Avaliar".
        \App\Models\Review::firstOrCreate(
            ['service_id' => $service->id, 'user_id' => $service->patient_id],
            ['rating' => null, 'comment' => null]
        );
        \App\Models\Review::firstOrCreate(
            ['service_id' => $service->id, 'user_id' => $service->professional_id],
            ['rating' => null, 'comment' => null]
        );

        Log::record('service.complete', "Service #{$service->id}");

        return back()->with('success', 'Serviço marcado como concluído.');
    }

    /**
     * Exporta o serviço como ficheiro iCalendar (.ics) para o calendário do utilizador.
     */
    public function exportIcs(Service $service)
    {
        abort_unless(
            $service->patient_id === Auth::id() || $service->professional_id === Auth::id(),
            403
        );

        $start = \Carbon\Carbon::parse(
            $service->date->format('Y-m-d') . ' ' . $service->time->format('H:i')
        );
        $end = $start->copy()->addHour();

        $escape = fn ($v) => preg_replace('/([,;\\\\])/', '\\\\$1', str_replace(["\r\n", "\r", "\n"], '\\n', (string) $v));

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Cura//Cura Health//PT',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:cura-service-' . $service->id . '@cura.pt',
            'DTSTAMP:' . now()->utc()->format('Ymd\\THis\\Z'),
            'DTSTART:' . $start->utc()->format('Ymd\\THis\\Z'),
            'DTEND:' . $end->utc()->format('Ymd\\THis\\Z'),
            'SUMMARY:' . $escape('Cura — ' . $service->service_type),
            'LOCATION:' . $escape($service->location),
            'DESCRIPTION:' . $escape($service->report ?? ''),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return response(implode("\r\n", $lines) . "\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="cura-service-' . $service->id . '.ics"',
        ]);
    }

    /**
     * Show the form for creating a new service (Patient view).
     */
    public function create()
    {
        $catalog = $this->catalog;
        return view('app.service.create', compact('catalog'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        // The validated() method returns only fields validated in StoreServiceRequest rules
        $validated = $request->validated();

        // Verify if service type exists in catalog
        if (!array_key_exists($validated['service_type'], $this->catalog)) {
            return back()->withErrors(['service_type' => 'Serviço inválido.']);
        }

        $selectedService = $this->catalog[$validated['service_type']];

        $service = Service::create([
            'patient_id'   => Auth::id(),
            'service_type' => $selectedService['label'],
            'price'        => $selectedService['price'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'location'     => $validated['location'],
            // Use $validated['notes'] instead of $request->notes for security
            'report'       => $validated['notes'] ?? null,
            'status'       => 'pending',
            'professional_id' => null,
        ]);

        Log::record('service.create', "Service #{$service->id} ({$service->service_type})");

        return redirect()
            ->route('app.index') // Redirect to main app dashboard
            ->with('success', 'Serviço agendado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // Verificar permissões...
        if ($service->patient_id !== Auth::id() && $service->professional_id !== Auth::id()) {
            abort(403);
        }

        // Carregar os dados do profissional para mostrar no card
        $service->load('professional'); 

        return view('app.service.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        // Ensure user can only edit their own services
        if ($service->patient_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $catalog = $this->catalog;
        return view('app.service.edit', compact('service', 'catalog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Security: Check if service belongs to logged-in user
        if ($service->patient_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        // Check catalog to update price and label (in case user changed service type)
        if (!array_key_exists($validated['service_type'], $this->catalog)) {
            return back()->withErrors(['service_type' => 'Serviço inválido.']);
        }
        
        $selectedService = $this->catalog[$validated['service_type']];

        // Patient só pode editar enquanto o serviço está pending; status é gerido por accept/markCompleted/destroy.
        if ($service->status !== 'pending') {
            return back()->with('error', 'Apenas serviços pendentes podem ser editados.');
        }

        $service->update([
            'service_type' => $selectedService['label'],
            'price'        => $selectedService['price'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'location'     => $validated['location'],
            'report'       => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('app.service.index') // Redirect to list
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    /**
     * Cancel (soft-delete) the specified service.
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();

        // Only the patient or assigned professional can cancel
        if ($service->patient_id !== $user->id && $service->professional_id !== $user->id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Validate business rules (status check + time restrictions)
        if (!$service->canBeCancelled()) {
            return back()->with('error', 'Este serviço não pode ser cancelado. Verifique o prazo de antecedência necessário.');
        }

        $service->update(['status' => 'canceled']);

        Log::record('service.cancel', "Service #{$service->id} cancelled by user #{$user->id}");

        return redirect()
            ->route('app.index')
            ->with('success', 'Serviço cancelado com sucesso.');
    }
}