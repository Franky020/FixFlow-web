<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeedbackController extends Controller
{
    /** Mostrar lista de comentarios */
    public function index()
    {
        $url = 'https://fixflow-endpoints.onrender.com/api/satisfaction/';

        try {
            $response = Http::get($url);
            $comments = $response->successful() ? $response->json() : [];
            $error = !$response->successful()
                ? 'Error al cargar los comentarios: ' . $response->status()
                : null;

            return view('feed.feedback', compact('comments', 'error'));
        } catch (\Exception $e) {
            return view('feed.feedback', [
                'comments' => [],
                'error' => 'Error de conexión: ' . $e->getMessage()
            ]);
        }
    }

    /** Mostrar formulario público (tu vista /feed) */
    public function create()
    {
        return view('publico.feed');
    }

    /** Guardar feedback en API externa */
    public function store(Request $request)
{
    $request->validate([
        'ticket_id' => ['required', 'integer', 'min:1'],
        'rating' => ['required', 'numeric', 'min:0', 'max:5'],
        'message' => ['nullable', 'string', 'max:500'],
    ]);

    $apiPayload = [
        "ticket"     => (int) $request->ticket_id,
        "rating"     => $request->input('rating') + 0,
        "message"    => $request->message ?? "Sin mensaje",
        "created_at" => now()->toIso8601String(),
    ];

    try {
        $response = Http::post('https://fixflow-endpoints.onrender.com/api/satisfaction/', $apiPayload);

        if ($response->successful() || $response->status() === 201) {
            return redirect()->route('feed')
                ->with('success', "¡Gracias por tu opinión!");
        }

        return back()->with('error', "Error al enviar feedback: " . json_encode($response->json()))
            ->withInput();

    } catch (\Exception $e) {
        return back()->with('error', "Error de conexión: " . $e->getMessage())
            ->withInput();
    }
}
}
