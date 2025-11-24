<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Session; 

class ApiController extends Controller
{
    /**
     * Helper para construir el cliente HTTP con el token de autenticación.
     * Retorna un objeto Illuminate\Http\Client\PendingRequest.
     */
    private function getApiHttp()
    {
        $token = Session::get('access_token');
        $http = Http::timeout(10);

        if ($token) {
            // Añadir el encabezado Authorization con el Bearer Token
            $http = $http->withToken($token);
            // Log para confirmar que el token se está cargando
            Log::debug('Token de acceso cargado para API Request.');
        } else {
            Log::warning('API Request sin Token de Autenticación. Posible 401 en Django.');
        }

        return $http;
    }

    private function getTicketValidationRules()
    {
        return [
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string', 
            'category'      => 'nullable|string', 
            'priority'      => 'nullable|string', 
            'equipment'     => 'nullable|string|max:150',
            'start_time'    => 'nullable|date',
            'end_time'      => 'nullable|date|after_or_equal:start_time',
            'duration'      => 'nullable|string', 
            'report'        => 'nullable|string',
            'status'        => 'nullable|string', 
            'company'       => 'required|integer', 
            'user'          => 'required|integer', 
            'location'      => 'nullable|integer', 
        ];
    }
    
    /**
     * Helper para formatear datos de tickets, eliminando vacíos y formateando fechas.
     */
    private function formatTicketApiData(array $validatedData)
    {
        $payload = [];
        foreach ($validatedData as $key => $value) {
            // Eliminar valores nulos/vacíos para evitar errores con la API externa (DRF).
            if (!is_null($value) && $value !== '') {
                $payload[$key] = $value;
            }
        }
        
        // Formatear fechas a ISO 8601.
        if (isset($payload['start_time'])) {
            $payload['start_time'] = Carbon::parse($payload['start_time'])->toISOString();
        }
        if (isset($payload['end_time'])) {
            $payload['end_time'] = Carbon::parse($payload['end_time'])->toISOString();
        }

        return $payload;
    }
    
    /**
     * Users Management
     * -------------------------------------------------------------------------
     */

    // Mapeos de valores de la UI (Español) a la API (Inglés minúsculas)
    private $userTypeMap = [
        'Super Admin' => 'super_admin',
        'Admin'       => 'admin',
        'Normal User' => 'normal_user',
    ];

    private $statusMap = [
        'Activo'      => 'active', 
        'Inactivo'    => 'inactive', 
        'Pendiente'   => 'pending', 
    ];

    // Obtener lista de users (MODIFICADO para usar getApiHttp)
    public function getUsers()
    {
        $baseUrl = env('API_URL');
        $data = [];
        $companies = [];

        // 1. Obtener lista de usuarios
        $usersResponse = $this->getApiHttp()->get($baseUrl . 'users/');
        if ($usersResponse->successful()) {
            $data = $usersResponse->json();
        } else {
            Log::error('API Get Users Failed', ['status' => $usersResponse->status() ?? 'N/A']);
            return view('user.index', compact('data', 'companies'))->withErrors(['api_error' => 'No se pudo conectar con el servicio de usuarios de la API.']);
        }

        // 2. Obtener lista de compañías (NUEVO)
        $companiesResponse = $this->getApiHttp()->get($baseUrl . 'companies/');
        if ($companiesResponse->successful()) {
            $companies = $companiesResponse->json();
        } else {
            Log::warning('API Get Companies Failed', ['status' => $companiesResponse->status() ?? 'N/A']);
        }

        // 3. Pasar users y companies a la vista
        return view('user.index', compact('data', 'companies'));
    }

    // Crear un user (CORREGIDO: Usando getApiHttp y manejo de multipart)
    public function createUser(Request $request)
    {
        // 1. Validar datos
        $request->validate([
            'username'      => 'required|string',
            'email'         => 'required|email',
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'phone'         => 'required|string',
            'address'       => 'required|string',
            'user_type'     => 'required|string|in:Super Admin,Admin,Normal User',
            'status'        => 'required|string|in:Activo,Inactivo,Pendiente',
            'age'           => 'required|integer|min:1',
            'rfc'           => 'required|string',
            'password'      => 'required|string',
            'company'       => 'required|integer',
            'photo'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 2. Payload sin incluir photo todavía
        $payload = [
            'username'   => $request->username,
            'email'      => $request->email,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'user_type'  => $this->userTypeMap[$request->user_type],
            'status'     => $this->statusMap[$request->status], 
            'age'        => (int) $request->age,
            'rfc'        => $request->rfc,
            'password'   => $request->password,
            'company'    => (int) $request->company,
        ];

        // 3. Construir request multipart, INCLUYENDO EL TOKEN
        $http = $this->getApiHttp()->asMultipart();

        // Si hay archivo, adjuntarlo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $http->attach(
                'photo',
                fopen($file->getPathname(), 'r'),
                $file->getClientOriginalName()
            );
        }

        // 4. Enviar todo el payload junto
        foreach ($payload as $key => $value) {
            // Los campos que no son archivos se deben adjuntar como strings
            $http->attach($key, $value);
        }

        // 5. Consumir API
        $response = $http->post(env('API_URL').'users/');

        if ($response->successful()) {
            return redirect()->route('user.list')->with('success', 'Usuario creado exitosamente.');
        }

        Log::error("API User Creation Failed", ['status' => $response->status(), 'body' => $response->body()]);

        return back()->withErrors([
            'api_error' => 'Error al crear el usuario: '.$response->body()
        ])->withInput();
    }

    /**
     * ACTUALIZAR un usuario existente
     * CORREGIDO: Manejo condicional de multipart y uso de PATCH.
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'username'      => 'required|string',
            'email'         => 'required|email',
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'phone'         => 'required|string',
            'address'       => 'required|string',
            'user_type'     => 'required|string|in:Super Admin,Admin,Normal User',
            'status'        => 'required|string|in:Activo,Inactivo,Pendiente',
            'age'           => 'required|integer|min:1',
            'rfc'           => 'required|string',
            'company'       => 'required|integer',
            'photo'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'password'      => 'nullable|string'
        ]);

        // 1. Crear el payload de datos
        $payload = [
            'username'   => $request->username,
            'email'      => $request->email,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'user_type'  => $this->userTypeMap[$request->user_type],
            'status'     => $this->statusMap[$request->status],
            'age'        => (int) $request->age,
            'rfc'        => $request->rfc,
            'company'    => (int) $request->company,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->password;
        }

        $apiClient = $this->getApiHttp();
        $apiEndpoint = env('API_URL')."users/{$id}/";
        $hasFile = $request->hasFile('photo');

        try {
            if ($hasFile) {
                // 2. Si hay archivo: Usamos asMultipart y PATCH
                $http = $apiClient->asMultipart();
                
                // Adjuntar la foto
                $file = $request->file('photo');
                $http->attach(
                    'photo',
                    fopen($file->getPathname(), 'r'),
                    $file->getClientOriginalName()
                );
                
                // Adjuntar el resto de los datos del payload
                foreach ($payload as $key => $value) {
                    $http->attach($key, $value);
                }

                // Usamos PATCH (mejor para actualizaciones parciales con DRF y archivos)
                $response = $http->patch($apiEndpoint);

            } else {
                // 2. Si NO hay archivo: Usamos JSON y PATCH
                // Laravel automáticamente añade el token gracias a getApiHttp()
                $response = $apiClient->patch($apiEndpoint, $payload);
            }

            if ($response->successful()) {
                return redirect()->route('user.list')->with('success', 'Usuario actualizado.');
            }

            Log::error("API User Update Failed", [
                'status' => $response->status(), 
                'body' => $response->body(), 
                'id' => $id, 
                'has_file' => $hasFile
            ]);

            return back()->withErrors([
                'api_error' => 'Error al actualizar usuario: '.$response->body()
            ])->withInput();

        } catch (\Exception $e) {
            Log::error("API User Update Exception", ['error' => $e->getMessage()]);
            return back()->withErrors(['api_error' => 'Error de conexión o excepción interna.']);
        }
    }

    // ELIMINAR un usuario existente (CORREGIDO: Usando getApiHttp)
    public function deleteUser($id)
    {
        // Enviar la petición DELETE a la API externa
        // ✨ CORRECCIÓN: Agregando '/' al final del ID.
        $response = $this->getApiHttp()->delete(env('API_URL') . "users/{$id}/");

        if ($response->successful()) {
            // Redirigir a la lista de usuarios después de la eliminación
            return redirect()->route('user.list')->with('success', "Usuario ID: {$id} eliminado exitosamente.");
        } else {
            $statusCode = $response->status();
            Log::error('API User Delete Failed', ['status' => $statusCode, 'body' => $response->body(), 'id' => $id]);
            $displayError = "Error {$statusCode}: Falla al eliminar el usuario ID: {$id}. Revisa el log de Laravel para detalles.";
            
            return redirect()->back()->withErrors(['api_error' => $displayError]);
        }
    }

    //-------------------------------------------------------------------------
    //                          FUNCIONES AUXILIARES DE TICKETS
    //-------------------------------------------------------------------------

    /**
     * Aplica la conversión de formato a los datos de la API (priority y status a minúsculas y snake_case).
     */
    protected function formatApiData(array $validated)
    {
        // Mapeo de valores de Prioridad del formulario a la API
        $priorityMap = [
            'baja' => 'low', 
            'media' => 'medium', 
            'alta' => 'high', 
            'urgente' => 'urgent',
        ];

        // Mapeo de valores de Estatus del formulario a la API
        $statusMap = [
            'abierto' => 'abierto',
            'en curso' => 'en_curso', 
            'cerrado' => 'cerrado', 
            'en espera' => 'en_espera',
        ];
        
        // Priority: Convertir el valor a minúsculas y buscar en el mapa
        if (isset($validated['priority'])) {
            $normalizedPriority = strtolower($validated['priority']);
            $validated['priority'] = $priorityMap[$normalizedPriority] ?? $normalizedPriority;
        }
        
        // Status: Convertir el valor a minúsculas y buscar en el mapa
        if (isset($validated['status'])) {
            $normalizedStatus = strtolower($validated['status']);
            $validated['status'] = $statusMap[$normalizedStatus] ?? $normalizedStatus;
        }
        
        return $validated;
    }

    //-------------------------------------------------------------------------
    //                              Tickets Management
    //-------------------------------------------------------------------------

    /**
     * Obtener lista de tickets (CORREGIDO: Usando getApiHttp)
     */
    public function getTickets(Request $request) 
    {
        $tickets = []; 
        $editId = $request->query('edit_id');
        $ticketToEdit = null;
        

        try {
            $response = $this->getApiHttp()->get(env('API_URL') . 'tickets/');

            if ($response->successful()) {
                $tickets = $response->json();
            } 
            
            if ($editId) {
                $responseEdit = $this->getApiHttp()->get(env('API_URL') . "tickets/{$editId}/"); 
                $responseEdit->throw();
                $ticketToEdit = $responseEdit->json();
            }

        } catch (\Exception $e) {
            Log::error('API Ticket Listing Failed', ['error' => $e->getMessage()]);
            $request->session()->flash('api_error', 'No se pudieron cargar los tickets. Error de conexión.');
        }
        
        return view('tickets.index', compact('tickets', 'ticketToEdit'));
    }

    /**
     * Obtener detalles de un ticket específico para la VISTA de Detalles. (CORREGIDO: Usando getApiHttp)
     */
    public function getTicketDetailsForView($id)
    {
        try {
            $response = $this->getApiHttp()->get(env('API_URL') . "tickets/{$id}/"); 
            $response->throwIfStatus([400, 404, 500]); 

            $ticket = $response->json();
            
            return view('tickets.details', compact('ticket'));

        } catch (\Exception $e) {
            $message = "Ticket #{$id} no encontrado o error de conexión.";
            if ($e instanceof \Illuminate\Http\Client\RequestException && ($e->response->status() ?? 0) == 404) {
                $message = "Ticket #{$id} no encontrado.";
            }
            
            Log::error('API Ticket Details Failed', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('tickets.index')->withErrors(['error' => $message]);
        }
    }


    /**
     * Crear un ticket. (CORREGIDO: Usando getApiHttp)
     */
    public function createTicket(Request $request)
    {
        $rules = $this->getTicketValidationRules();
        $validated = $request->validate($rules);
        $payload = $this->formatApiData($validated);
        
        try {
            // Usamos getApiHttp() para incluir el token
            $response = $this->getApiHttp()->post(env('API_URL') . 'tickets/', $payload);

            if ($response->successful()) {
                return redirect()->route('tickets.index')->with('success', 'Ticket creado exitosamente.');
            } else {
                $statusCode = $response->status();
                $errorDetail = $response->json();
                
                $displayError = "Error {$statusCode}: Falla al crear el ticket.";
                $displayError .= (!empty($errorDetail) && is_array($errorDetail)) 
                                    ? " Detalles de la API: " . json_encode($errorDetail) 
                                    : " Revisa el log de Laravel para detalles.";

                Log::error('API Ticket Creation Failed', ['status' => $statusCode, 'body' => $response->body(), 'request_payload' => $payload]);
                
                return redirect()->back()->withInput()->withErrors(['api_error' => $displayError]);
            }
        } catch (\Exception $e) {
            Log::error('API Ticket Creation Exception', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['api_error' => 'Error de conexión o excepción interna.']);
        }
    }

    /**
     * Actualizar un ticket existente. (CORREGIDO: Usando getApiHttp)
     */
    public function updateTicket(Request $request, $id)
    {
        $rules = $this->getTicketValidationRules();
        $validated = $request->validate($rules);
        $payload = $this->formatApiData($validated);

        try {
            // Usamos getApiHttp() para incluir el token
            // Usamos PATCH que es ideal para actualizar, en lugar de PUT que es para reemplazar completamente
            $response = $this->getApiHttp()->patch(env('API_URL') . "tickets/{$id}/", $payload);

            if ($response->successful()) {
                return redirect()->route('tickets.index', ['edit_id' => $id])->with('success', "Ticket #{$id} actualizado exitosamente.");
            } else {
                $statusCode = $response->status();
                $errorDetail = $response->json();
                
                $displayError = "Error {$statusCode}: Falla al actualizar el ticket #{$id}.";
                $displayError .= (!empty($errorDetail) && is_array($errorDetail)) 
                                    ? " Detalles de la API: " . json_encode($errorDetail) 
                                    : " Revisa el log de Laravel para detalles.";

                Log::error('API Ticket Update Failed', ['status' => $statusCode, 'body' => $response->body(), 'id' => $id]);
                return redirect()->back()->withInput()->withErrors(['api_error' => $displayError]);
            }
        } catch (\Exception $e) {
            Log::error('API Ticket Update Exception', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['api_error' => 'Error de conexión o excepción interna.']);
        }
    }

    /**
     * Eliminar un ticket existente. (CORREGIDO: Usando getApiHttp)
     */
    public function deleteTicket($id)
    {
        try {
            // Usamos getApiHttp() para incluir el token
            $response = $this->getApiHttp()->delete(env('API_URL') . "tickets/{$id}/");

            if ($response->successful()) {
                return redirect()->route('tickets.index')->with('success', "Ticket #{$id} eliminado exitosamente.");
            } else {
                $statusCode = $response->status();
                $errorDetail = $response->json();
                
                $displayError = "Error {$statusCode}: Falla al eliminar el ticket #{$id}.";
                $displayError .= (!empty($errorDetail) && is_array($errorDetail)) 
                                    ? " Detalles de la API: " . json_encode($errorDetail) 
                                    : " Revisa el log de Laravel para detalles.";

                Log::error('API Ticket Delete Failed', ['status' => $statusCode, 'body' => $response->body(), 'id' => $id]);
                return redirect()->back()->withErrors(['api_error' => $displayError]);
            }
        } catch (\Exception $e) {
            Log::error('API Ticket Delete Exception', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['api_error' => 'Error de conexión o excepción interna.']);
        }
    }
    
    //-------------------------------------------------------------------------
    //                       Spare Parts Management
    //-------------------------------------------------------------------------
    
    // Obtener lista de piezas de recambio (CORREGIDO: Usando getApiHttp)
    public function getSpareParts()
    {
        $baseUrl = env('API_URL');
        $spareParts = [];
        $companies = [];

        // 1. Obtener lista de piezas
        $partsResponse = $this->getApiHttp()->get($baseUrl . 'spare_parts/');
        if ($partsResponse->successful()) {
            $spareParts = $partsResponse->json();
        } else {
            Log::warning('API Get Spare Parts Failed', ['status' => $partsResponse->status() ?? 'N/A']);
        }

        // 2. Obtener lista de compañías (NUEVO)
        $companiesResponse = $this->getApiHttp()->get($baseUrl . 'companies/');
        if ($companiesResponse->successful()) {
            $companies = $companiesResponse->json();
        } else {
            Log::warning('API Get Companies Failed', ['status' => $companiesResponse->status() ?? 'N/A']);
        }

        // Retorna la vista principal del dashboard de piezas, enviando companies
        return view('spare_parts.index', compact('spareParts', 'companies'));
    }

    // Obtener detalles de una pieza para la VISTA de Edición/Detalles (CORREGIDO: Usando getApiHttp)
    public function getSparePartDetailsForView($id)
    {
        $baseUrl = env('API_URL');

        // 1. Obtener detalles de la pieza
        $partResponse = $this->getApiHttp()->get($baseUrl . "spare_parts/{$id}/");

        if (!$partResponse->successful()) {
            return redirect()->route('spare_parts.index')->withErrors(['error' => "Pieza #{$id} no encontrada o error de conexión."]);
        }
        $sparePart = $partResponse->json();

        // 2. Obtener lista de compañías 
        $companiesResponse = $this->getApiHttp()->get($baseUrl . 'companies/');
        $companies = $companiesResponse->successful() ? $companiesResponse->json() : [];
        
        // Retorna la vista 'spare_parts.details', incluyendo companies
        return view('spare_parts.details', compact('sparePart', 'companies'));
    }

    // Crear una nueva pieza de recambio (CORREGIDO: Usando getApiHttp)
    public function createSparePart(Request $request)
    {
        // ... (Validación y preparación de payload sin cambios) ...
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'min_stock'     => 'nullable|integer|min:0',
            'stock'         => 'nullable|integer|min:0',
            'status'        => 'required|string', 
            'company'       => 'required|integer', 
        ]);

        $payload = $request->only([
            'name', 'description', 'serial_number', 'min_stock', 
            'stock', 'company'
        ]);
        
        $payload['company'] = (int)$payload['company'];
        $payload['min_stock'] = isset($payload['min_stock']) ? (int)$payload['min_stock'] : null;
        $payload['stock'] = isset($payload['stock']) ? (int)$payload['stock'] : null;

        $uiStatus = $request->input('status');
        $payload['status'] = $this->statusMap[$uiStatus] ?? $uiStatus; 

        // 3. Enviar a la API (Usando getApiHttp)
        $response = $this->getApiHttp()->post(env('API_URL') . 'spare_parts/', $payload);

        // 4. Manejar la respuesta
        if ($response->successful()) {
            return redirect()->route('spare_parts.index')->with('success', 'Pieza de recambio creada exitosamente.');
        } else {
            $statusCode = $response->status();
            $responseBody = $response->body(); 
            
            Log::error('API Spare Part Creation Failed', [
                'status' => $statusCode, 
                'body' => $responseBody,
                'request_payload' => $payload,
            ]);
            
            $displayError = "Error {$statusCode}: Falla al crear la pieza. Revisa el log de Laravel para detalles. Mensaje API: " . substr($responseBody, 0, 100) . "...";
            return redirect()->back()->withInput()->withErrors(['api_error' => $displayError]);
        }
    }

    // Actualizar una pieza existente (CORREGIDO: Usando getApiHttp y PATCH)
    public function updateSparePart(Request $request, $id)
    {
        // ... (Validación y preparación de payload sin cambios) ...
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'serial_number' => 'nullable|string|max:255',
            'min_stock'     => 'nullable|integer|min:0',
            'stock'         => 'nullable|integer|min:0',
            'status'        => 'required|string', 
            'company'       => 'required|integer',
        ]);

        $payload = $request->only([
            'name', 'description', 'serial_number', 'min_stock', 
            'stock', 'company'
        ]);
        
        $payload['company'] = (int)$payload['company'];
        $payload['min_stock'] = isset($payload['min_stock']) ? (int)$payload['min_stock'] : null;
        $payload['stock'] = isset($payload['stock']) ? (int)$payload['stock'] : null;

        $uiStatus = $request->input('status');
        $payload['status'] = $this->statusMap[$uiStatus] ?? $uiStatus;
        
        // 3. Enviar a la API externa (Usando getApiHttp y PATCH)
        $response = $this->getApiHttp()->patch(env('API_URL') . "spare_parts/{$id}/", $payload);

        // 4. Manejar la respuesta
        if ($response->successful()) {
            return redirect()->route('spare_parts.details', $id)->with('success', "Pieza #{$id} actualizada exitosamente.");
        } else {
            $statusCode = $response->status();
            $responseBody = $response->body();
            
            Log::error('API Spare Part Update Failed', ['status' => $statusCode, 'body' => $responseBody, 'id' => $id]);
            
            $displayError = "Error {$statusCode}: Falla al actualizar la pieza. Revisa el log de Laravel para detalles. Mensaje API: " . substr($responseBody, 0, 100) . "...";
            return redirect()->back()->withInput()->withErrors(['api_error' => $displayError]);
        }
    }

    // Eliminar una pieza existente (CORREGIDO: Usando getApiHttp)
    public function deleteSparePart($id)
    {
        // Enviar la petición DELETE a la API externa (Usando getApiHttp)
        $response = $this->getApiHttp()->delete(env('API_URL') . "spare_parts/{$id}/");

        if ($response->successful()) {
            return redirect()->route('spare_parts.index')->with('success', "Pieza #{$id} eliminada exitosamente.");
        } else {
            $statusCode = $response->status();
            Log::error('API Spare Part Delete Failed', ['status' => $statusCode, 'body' => $response->body(), 'id' => $id]);
            $displayError = "Error {$statusCode}: Falla al eliminar la pieza. Revisa el log de Laravel para detalles.";
            return redirect()->back()->withErrors(['api_error' => $displayError]);
        }
    }
}