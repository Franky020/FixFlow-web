<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InicioDController extends Controller
{
    /* =========================================
       EMPRESAS + PLANES ACTIVOS
    ==========================================*/
    public function companiesStats()
    {
        $token = Session::get('access_token');

        if (!$token) {
            return response()->json([
                "error" => "No token in session"
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Accept'        => 'application/json'
            ])->get("https://fixflow-endpoints.onrender.com/api/companies/stats/");

            if ($response->serverError()) {
                return response()->json([
                    "error" => "API ERROR 500",
                    "raw_error" => $response->body()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Exception",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    /* =========================================
       ROLES POR COMPAÑÍA (ADMIN)
    ==========================================*/
    public function rolesStats()
    {
        $token = Session::get('access_token');

        if (!$token) {
            return response()->json([
                "error" => "No token in session"
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Accept'        => 'application/json'
            ])->get("https://fixflow-endpoints.onrender.com/api/users/user_type_counts/");

            if ($response->serverError()) {
                return response()->json([
                    "error" => "API ERROR 500",
                    "raw_error" => $response->body()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Exception",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    /* =========================================
       TICKETS POR ESTADO
    ==========================================*/
    public function ticketsStatusStats()
    {
        $token = Session::get('access_token');

        if (!$token) {
            return response()->json([
                "error" => "No token in session"
            ], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Accept'        => 'application/json'
            ])->get("https://fixflow-endpoints.onrender.com/api/tickets/status-counts/");

            if ($response->serverError()) {
                return response()->json([
                    "error" => "API ERROR 500",
                    "raw_error" => $response->body()
                ], 500);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                "error"   => "Exception",
                "details" => $e->getMessage()
            ], 500);
        }
    }
}
