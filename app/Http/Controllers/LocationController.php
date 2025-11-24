<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        // URL limpia
        $this->baseUrl = rtrim(env('API_URL'), '/') . '/locations/';
    }

    /** 🔐 Helper para enviar token en cada request */
    private function api()
    {
        $token = session('access_token');

        return Http::withHeaders([
            'Authorization' => "Bearer {$token}"
        ]);
    }

    /** 🧩 Listado */
    public function index()
    {
        try {
            $response = $this->api()->get($this->baseUrl);
            $json = $response->json();

            // Normalizamos estructura (para evitar errores de string)
            if (isset($json['data']) && is_array($json['data'])) {
                $locations = $json['data'];
            } elseif (is_array($json)) {
                $locations = $json;
            } else {
                $locations = [];
            }

        } catch (\Exception $e) {
            $locations = [];
        }

        return view('locations.index', compact('locations'));
    }

    /** ➕ Crear */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'company' => 'required|integer',
        ]);

        try {
            $this->api()->post($this->baseUrl, $validated)->throw();
            return redirect()->route('locations.index')->with('success', 'Localización registrada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar la localización.');
        }
    }

    /** ✏️ Editar */
    public function edit($id)
    {
        try {
            $response = $this->api()->get($this->baseUrl . $id);
            $json = $response->json();

            $location = $json['data'] ?? $json;

            if (!is_array($location)) {
                return redirect()->route('locations.index')->with('error', 'Respuesta inválida del servidor.');
            }

        } catch (\Exception $e) {
            return redirect()->route('locations.index')->with('error', 'No se pudo cargar la localización.');
        }

        return view('locations.edit', compact('location'));
    }

    /** 🔁 Actualizar */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'company' => 'required|integer',
        ]);

        try {
            $this->api()->put($this->baseUrl . $id, $validated)->throw();
            return redirect()->route('locations.index')->with('success', "Localización ID {$id} actualizada correctamente.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar la localización.');
        }
    }

    /** 🗑️ Eliminar */
    public function destroy($id)
    {
        try {
            $this->api()->delete($this->baseUrl . $id)->throw();
            return redirect()->route('locations.index')->with('success', 'Localización eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la localización.');
        }
    }
}
