<?php

namespace App\Http\Controllers;

use App\Models\Service;
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
        // 1. Verify if the service is still available
        if ($service->professional_id !== null) {
            return back()->with('error', 'Este serviço já foi aceite por outro profissional.');
        }

        // 2. Assign the service to the logged-in user (Professional)
        $service->update([
            'professional_id' => Auth::id(),
            'status' => 'confirmed' // Change status to confirmed/active
        ]);

        return redirect()->route('app.service.index')->with('success', 'Serviço aceite com sucesso! Pode vê-lo na sua agenda.');
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

        // Create the record
        Service::create([
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

        // Update the record
        $service->update([
            'service_type' => $selectedService['label'],
            'price'        => $selectedService['price'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'location'     => $validated['location'],
            'report'       => $validated['notes'] ?? null,
            'status'       => $validated['status'], // Now we update the status too
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

        return redirect()
            ->route('app.index')
            ->with('success', 'Serviço cancelado com sucesso.');
    }
}