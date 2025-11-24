<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReportController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('API_URL'), '/') . '/reports/';
    }

    /**
     * Helper para cliente HTTP con token desde sesión
     */
    private function api()
    {
        $token = Session::get('access_token');

        return Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ]);
    }

    // LISTA DE REPORTES
    public function index()
    {
        $reports = [];
        $ticketsList = [];

        try {
            $responseReports = $this->api()->get($this->baseUrl);
            if ($responseReports->successful()) {
                $reports = $responseReports->json();
            }

            $responseTickets = $this->api()->get(env('API_URL') . 'tickets/');
            if ($responseTickets->successful()) {
                $ticketsList = $responseTickets->json();
            }

        } catch (\Exception $e) {
            Log::error("API Connection Failed", ['error' => $e->getMessage()]);
        }

        return view('reports.index', [
            'data' => $reports,
            'ticketsList' => $ticketsList
        ]);
    }

    // CREAR REPORTE
    public function create(Request $request)
    {
        $validated = $request->validate([
            'ticket' => 'required|integer'
        ]);

        try {
            $response = $this->api()->post($this->baseUrl, $validated);

            if ($response->failed()) {
                return back()->with('error', 'No se pudo registrar el reporte.');
            }

            return redirect()->route('reports.index')
                ->with('success', 'Reporte creado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    // ELIMINAR REPORTE
    public function delete($id)
    {
        try {
            $response = $this->api()->delete($this->baseUrl . $id . '/');

            if ($response->successful()) {
                return back()->with('success', 'Reporte eliminado.');
            }

            return back()->with('error', 'No se pudo eliminar el reporte.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    // GUARDAR MENSAJE DEL REPORTE
    public function storeMessage(Request $request, $reportId)
    {
        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('El mensaje no puede estar vacío.');
                    }
                }
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ]
        ]);

        try {
            $multipart = [
                [
                    'name' => 'message',
                    'contents' => $validated['message']
                ]
            ];

            if ($request->hasFile('image')) {
                $multipart[] = [
                    'name' => 'image',
                    'contents' => fopen($request->file('image')->getRealPath(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName()
                ];
            }

            $url = env('API_URL') . "reports/{$reportId}/add-message/";

            $response = $this->api()->asMultipart()->post($url, $multipart);

            if ($response->failed()) {
                Log::error("API ERROR ADD-MESSAGE", [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);

                $apiError = $response->json();
                $errorMessage = 'Error desconocido al enviar mensaje.';

                if (isset($apiError['detail'])) $errorMessage = $apiError['detail'];
                elseif (isset($apiError['message'])) $errorMessage = $apiError['message'];
                elseif (is_array($apiError)) {
                    $errorMessage = collect($apiError)->flatten()->implode(' | ');
                }

                return back()->with('error', 'Error API: ' . $errorMessage);
            }

            return back()->with('success', 'Mensaje agregado correctamente.');

        } catch (\Exception $e) {
            Log::error("EXCEPTION ADD-MESSAGE", ['error' => $e->getMessage()]);
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    // MOSTRAR REPORTE
    public function show($id)
    {
        try {
            $response = $this->api()->get($this->baseUrl . $id . '/');

            if ($response->successful()) {
                $report = $response->json();
                return view('reports.show', compact('report'));
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el reporte.');
        }

        return back()->with('error', 'Reporte no encontrado.');
    }

    // EXPORTAR PDF
    public function exportPdf($reportId)
    {
        try {
            $url = "https://fixflow-endpoints.onrender.com/api/reports/{$reportId}/export-pdf/";

            $response = $this->api()->get($url);

            if (!$response->successful()) {
                return back()->with('error', 'No se pudo generar el PDF desde la API.');
            }

            $pdfContent = $response->body();

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=reporte-{$reportId}.pdf");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con la API: ' . $e->getMessage());
        }
    }
}
