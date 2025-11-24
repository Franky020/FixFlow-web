<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://fixflow-endpoints.onrender.com/api/companies/'; 
    }

    /**
     * Helper para construir el cliente HTTP autenticado con token.
     */
    private function getApiHttp()
    {
        $token = Session::get('access_token');

        return Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ]);
    }

    protected function getPlanValidationValues()
    {
        return [
            'basic', 
            'premium', 
            'enterprise', 
            'custom', 
            'fixflow_internal' 
        ];
    }

    protected function getStatusValidationValues()
    {
        return ['active', 'inactive'];
    }

    protected function formatApiData(array $validated)
    {
        if (isset($validated['plan_type'])) {
            $validated['plan_type'] = strtolower(str_replace(' ', '_', $validated['plan_type']));
        }

        if (isset($validated['status'])) {
            $validated['status'] = strtolower($validated['status']);
        }

        return $validated;
    }

    /**
     * Mostrar listado + modo edición opcional
     */
    public function index(Request $request)
    {
        $data = [];
        $companyToEdit = null;
        $editId = $request->query('edit_id');

        try {
            $response = $this->getApiHttp()->get($this->baseUrl);
            $response->throw();

            $data = $response->json() ?? [];

        } catch (\Exception $e) {
            session()->flash('error', 'Error al conectar con la API para listar compañías.');
        }

        if ($editId) {
            try {
                $responseEdit = $this->getApiHttp()->get($this->baseUrl . $editId . '/');
                $responseEdit->throw();

                $companyToEdit = $responseEdit->json();

            } catch (\Exception $e) {
                session()->flash('error', "No se pudo cargar la compañía ID {$editId}.");
            }
        }

        return view('companies.index', compact('data', 'companyToEdit'));
    }

    /**
     * Crear nueva compañía
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|url|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'description' => 'required|string',
            'plan_type' => ['required', 'string', Rule::in($this->getPlanValidationValues())],
            'status' => ['required', 'string', Rule::in($this->getStatusValidationValues())],
        ]);

        $validated = $this->formatApiData($validated);

        try {
            $response = $this->getApiHttp()->post($this->baseUrl, $validated);
            $response->throw();

            return redirect()->route('companies.index')->with('success', 'Compañía registrada exitosamente.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Fallo al registrar la compañía.');
        }
    }

    /**
     * Activa modo edición
     */
    public function edit($id)
    {
        return redirect()->route('companies.index', ['edit_id' => $id]);
    }

    /**
     * Actualizar compañía
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|url|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'description' => 'required|string',
            'plan_type' => ['required', 'string', Rule::in($this->getPlanValidationValues())],
            'status' => ['required', 'string', Rule::in($this->getStatusValidationValues())],
        ]);

        $validated = $this->formatApiData($validated);

        try {
            $response = $this->getApiHttp()->put($this->baseUrl . $id . '/', $validated);

            if ($response->failed()) {
                $status = $response->status();
                $detail = $response->json();

                $msg = "Fallo al actualizar. Código {$status}. ";

                if (is_array($detail)) {
                    $msg .= "Detalles: " . json_encode($detail);
                } else {
                    $msg .= "Mensaje: " . substr(strip_tags($response->body()), 0, 200) . '...';
                }

                return back()->withInput()->with('error', $msg);
            }

            return redirect()->route('companies.index')
                             ->with('success', "Compañía ID {$id} actualizada exitosamente.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar compañía
     */
    public function destroy($id)
    {
        try {
            $response = $this->getApiHttp()->delete($this->baseUrl . $id . '/');
            $response->throw();

            return redirect()->route('companies.index')
                             ->with('success', "Compañía ID {$id} eliminada exitosamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Fallo al eliminar la compañía.');
        }
    }
}
