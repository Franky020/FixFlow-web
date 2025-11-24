<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginApiController extends Controller
{
    private const AUTH_API_URL = "https://fixflow-endpoints.onrender.com/api/login/";
    private const REFRESH_API_URL = "https://fixflow-endpoints.onrender.com/api/token/refresh/";

    /**
     * Login usando API externa y guardando tokens en sesión.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        try {
            $response = Http::post(self::AUTH_API_URL, [
                'email'    => $request->email,
                'password' => $request->password
            ]);

            if ($response->failed()) {
                $error = $response->json('detail') 
                         ?? $response->json('error') 
                         ?? $response->json('message') 
                         ?? 'Credenciales inválidas.';
                
                return redirect()->back()->with('error', $error);
            }

            $data = $response->json();

            // Obtener usuario
            $user = $data['user'] ?? [];

            // ✔ Extraer rol
            $role = $user['role'] ?? null;

            // Guardar tokens, usuario y rol en sesión
            session([
                'access_token'  => $data['access'] ?? null,
                'refresh_token' => $data['refresh'] ?? null,
                'user_data'     => $user,
                'user_type'     => $role,   // ← ✔ SE GUARDA AQUÍ
            ]);

            // Redirigir al dashboard
            return redirect()->route('dashboards');

        } catch (\Exception $e) {
            Log::error("Login Exception: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión con el servidor de autenticación.');
        }
    }

    /**
     * Refrescar token de acceso usando refresh token.
     */
    public function refreshToken(): ?string
    {
        $refreshToken = session('refresh_token');

        if (empty($refreshToken)) {
            Log::warning("No hay refresh token en sesión.");
            return null;
        }

        try {
            $response = Http::post(self::REFRESH_API_URL, ['refresh' => $refreshToken]);

            if ($response->successful()) {
                $newAccessToken = $response->json('access');
                if ($newAccessToken) {
                    session(['access_token' => $newAccessToken]);
                    return $newAccessToken;
                }
            }

            Log::warning("Fallo al refrescar token:", [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error("Excepción al refrescar token: " . $e->getMessage());
            return null;
        }
    }
}
