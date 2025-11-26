<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FeedbackController extends Controller
{
    /** Cargar lista de comentarios desde API */
    public function index()
    {
        $token = Session::get('access_token');

        if (!$token) {
            return view('feed.feedback', [
                'comments' => [],
                'error' => 'No hay token de autenticación en la sesión.'
            ]);
        }

        try {
            $response = Http::withToken($token)
                ->get('https://fixflow-endpoints.onrender.com/api/satisfaction/');

            if ($response->status() === 401) {
                return view('feed.feedback', [
                    'comments' => [],
                    'error' => 'Token expirado o inválido. Inicia sesión nuevamente.'
                ]);
            }

            $comments = $response->successful() ? $response->json() : [];

            return view('feed.feedback', [
                'comments' => $comments,
                'error' => $response->successful() ? null : "Error: " . $response->status()
            ]);

        } catch (\Exception $e) {
            return view('feed.feedback', [
                'comments' => [],
                'error' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }

    /** Formulario público (vista /feed) */
    public function create()
    {
        return view('publico.feed');
    }

    /** Guardar feedback usando token */
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'integer', 'min:1'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $token = Session::get('access_token');

        if (!$token) {
            return back()->with('error', 'No hay token de autenticación. Inicia sesión.');
        }

        $apiPayload = [
            "ticket"     => (int) $request->ticket_id,
            "rating"     => $request->rating + 0,
            "message"    => $request->message ?? "Sin mensaje",
            "created_at" => now()->toIso8601String(),
        ];

        try {
            $response = Http::withToken($token)
                ->post('https://fixflow-endpoints.onrender.com/api/satisfaction/', $apiPayload);

            // Token inválido / expirado
            if ($response->status() === 401) {
                return back()->with('error', 'Tu sesión expiró. Inicia sesión nuevamente.');
            }

            // Exitoso
            if ($response->successful() || $response->status() === 201) {
                return redirect()->route('feed')
                    ->with('success', '¡Gracias por tu opinión!');
            }

            // Otros errores
            return back()->with('error', "Error API: " . json_encode($response->json()))
                ->withInput();

        } catch (\Exception $e) {
            return back()->with('error', "Error de conexión: " . $e->getMessage())
                ->withInput();
        }
    }
}
