<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    // URL de la API externa para obtener el usuario autenticado
    private $meApiUrl = 'https://fixflow-endpoints.onrender.com/api/me/'; 

    /**
     * Actúa como proxy para obtener los datos del usuario logueado.
     */
    public function getAuthenticatedUser(Request $request): JsonResponse
    {
        // 1. Obtener el token de acceso del encabezado Authorization del request
        // Tu frontend DEBE enviar el token de localStorage en el encabezado Authorization
        $accessToken = $request->bearerToken(); 

        if (!$accessToken) {
            return response()->json(['error' => 'Token no proporcionado.'], 401);
        }

        try {
            // 2. Enviar el token a la API externa
            $response = Http::withToken($accessToken)
                             ->timeout(10)
                             ->get($this->meApiUrl);

            // 3. Devolver la respuesta de la API externa
            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error de conexión con la API de usuario.',
            ], 500);
        }
    }
}