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
        'auxiliar' => ['label' => 'Auxiliar de Saúde', 'price' => 25.00],
        'enfermagem' => ['label' => 'Enfermagem', 'price' => 45.00],
        'medica' => ['label' => 'Consulta Médica', 'price' => 80.00],
        'fisioterapia' => ['label' => 'Fisioterapia', 'price' => 50.00],
    ];
    

    public function index(Request $request)
    {
        // Buscar serviços que:
        // Estão pendentes
        // NÃO têm profissional atribuído
        // NÃO foram criados por mim (Profissional não pode aceitar o próprio serviço)
        
        $query = Service::where('status', 'pending')
                        ->whereNull('professional_id')
                        ->where('patient_id', '!=', Auth::id()) // Opcional: não aceitar o próprio serviço
                        ->with('patient'); // Carregar dados do Utente (Nome, etc)

        // Filtro (se quiseres manter o filtro da imagem)
        if ($request->filled('filter')) {
             $query->where('service_type', 'like', '%' . $request->filter . '%');
        }

        $services = $query->orderBy('date', 'asc')->get();

        return view('app.service.index', compact('services'));
    }

    public function accept(Service $service)
    {
        // Verificar se já não foi apanhado por outro
        if ($service->professional_id !== null) {
            return back()->with('error', 'Este serviço já foi aceite por outro profissional.');
        }

        // Atribuir ao user logado (Profissional)
        $service->update([
            'professional_id' => Auth::id(),
            'status' => 'confirmed' // Passa a confirmado/ativo
        ]);

        return back()->with('success', 'Serviço aceite com sucesso! Pode vê-lo na sua agenda.');
    }

    public function create()
    {
        $catalog = $this->catalog;
        return view('app.service.create', compact('catalog'));
    }

    public function store(StoreServiceRequest $request)
    {
        // O método validated() devolve apenas os campos que estão validados nas Rules do StoreServiceRequest
        $validated = $request->validated();

        // Verificar se o tipo de serviço existe no catálogo
        if (!array_key_exists($validated['service_type'], $this->catalog)) {
            return back()->withErrors(['service_type' => 'Serviço inválido.']);
        }

        $selectedService = $this->catalog[$validated['service_type']];

        // Criar o registo
        Service::create([
            'patient_id'   => Auth::id(),
            'service_type' => $selectedService['label'],
            'price'        => $selectedService['price'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'location'     => $validated['location'],
            // Usa $validated['notes'] em vez de $request->notes para garantir segurança
            'report'       => $validated['notes'] ?? null, 
            'status'       => 'pending',
            'professional_id' => null,
        ]);

        return redirect()
            ->route('app.service.create')
            ->with('success', 'Serviço agendado com sucesso!');
    }

    public function show(Service $service)
    {
        // Garantir que o utilizador só pode ver os seus próprios serviços
        if ($service->patient_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('app.service.show', compact('service'));
    }

    public function edit(Service $service)
    {
        // Garantir que o utilizador só pode editar os seus próprios serviços
        if ($service->patient_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $catalog = $this->catalog;
        return view('app.service.edit', compact('service', 'catalog'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Segurança: Verificar se o serviço pertence ao user logado
        if ($service->patient_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        // Verificar catálogo para atualizar preço e label (caso o user mude o tipo de serviço)
        if (!array_key_exists($validated['service_type'], $this->catalog)) {
            return back()->withErrors(['service_type' => 'Serviço inválido.']);
        }
        
        $selectedService = $this->catalog[$validated['service_type']];

        // Atualizar o registo
        $service->update([
            'service_type' => $selectedService['label'],
            'price'        => $selectedService['price'],
            'date'         => $validated['date'],
            'time'         => $validated['time'],
            'location'     => $validated['location'],
            'report'       => $validated['notes'] ?? null,
            'status'       => $validated['status'], // Agora atualizamos também o estado
        ]);

        return redirect()
            ->route('app.service.index') // Redireciona para a lista
            ->with('success', 'Serviço atualizado com sucesso!');
    }
}