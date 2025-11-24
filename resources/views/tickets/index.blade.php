<x-app-layout>
    {{-- Encabezado de la página --}}
    <x-slot name="header">
        <div class="border-l-4 border-ff-secondary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Administración de Tickets de Soporte') }}
            </h2>
        </div>
    </x-slot>

    {{-- Fondo Gris Claro de FixFlow --}}
    <div class="py-12 bg-ff-bg-light min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de Éxito/Error --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-ff-success text-ff-dark p-4 rounded-lg mb-8" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-ff-error text-ff-dark p-4 rounded-lg mb-8" role="alert">
                    <p class="font-bold">Error en la Operación</p>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if($errors->has('api_error'))
                            <li>**Error de API:** {{ $errors->first('api_error') }}</li>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- LÓGICA DE EDICIÓN/CREACIÓN (PHP) --}}
            @php
                // Determina si estamos editando
                $isEditing = isset($ticketToEdit);
                // Determina la acción y el método
                $formAction = $isEditing 
                    ? route('tickets.update', $ticketToEdit['id']) 
                    : route('tickets.create');
                $method = $isEditing ? 'PUT' : 'POST';
                
                /**
                 * Helper para obtener el valor actual del campo (old() o $ticketToEdit).
                 * Establece 'abierto' como defecto para 'status' en modo creación.
                 */
                $getValue = function($field, $default = '') use ($ticketToEdit, $isEditing) {
                    if ($field === 'status' && !$isEditing) {
                        $default = 'abierto'; // Forzar valor válido en español para la API
                    }
                    return old($field, $ticketToEdit[$field] ?? $default);
                };
                
                // Función para formatear fechas para input type="datetime-local"
                $formatDate = function($dateString) {
                    if (empty($dateString)) return '';
                    try {
                        // Carbon::parse maneja el formato ISO 8601 de tu API
                        return \Carbon\Carbon::parse($dateString)->format('Y-m-d\TH:i');
                    } catch (\Exception $e) {
                        return '';
                    }
                };

                // Opciones para SELECTs (Valor enviado a la API => Etiqueta mostrada)
                
                // PRIORITY: Valores de la API (none, low, medium, high)
                $priorityOptions = [
                    'none' => 'Ninguna', 
                    'low' => 'Baja', 
                    'medium' => 'Media', 
                    'high' => 'Alta'
                ]; 
                
                // CATEGORY: Valores de la API (corrective, preventive, predictive, none)
                $categoryOptions = [
                    'corrective' => 'Correctivo', 
                    'preventive' => 'Preventivo', 
                    'predictive' => 'Predictivo', 
                    'none' => 'Ninguna'
                ]; 

                // STATUS: Valores de la API (abierto, en_curso, cerrado, en_espera)
                $statusOptions = [
                    'abierto' => 'Abierto', 
                    'en_curso' => 'En Curso', 
                    'cerrado' => 'Cerrado', 
                    'en_espera' => 'En Espera'
                ]; 

                // DURACION: Descomponemos el formato HH:MM:SS para edición
                $durationString = $getValue('duration', '00:00:00');
                $durationParts = explode(':', $durationString);
                $currentHours = count($durationParts) > 2 ? intval($durationParts[0]) : 0;
                $currentMinutes = count($durationParts) > 1 ? intval($durationParts[1]) : 0;
                $currentSeconds = count($durationParts) > 2 ? intval(floor($durationParts[2])) : 0;

                $hoursOptions = range(0, 23);
                $minuteSecondOptions = range(0, 59);
            @endphp
            
            {{-- 🧡 Sección de Formulario de Creación/Edición --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 {{ $isEditing ? 'border-ff-error' : 'border-ff-primary' }} mb-12">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-200 pb-3">
                    @if($isEditing)
                        ✏️ Editar Ticket #{{ $ticketToEdit['id'] }} <span class="text-ff-error">| Modo Edición</span>
                    @else
                        Crear Nuevo Ticket <span class="text-ff-primary">| Detalle de Incidencia</span>
                    @endif
                </h2>
                
                <form method="POST" action="{{ $formAction }}" class="space-y-6">
                    @csrf
                    @method($method)
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        {{-- Campo: title --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Título del Ticket:</label>
                            <input type="text" name="title" required 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" placeholder="Ej: Falla eléctrica en servidor." value="{{ $getValue('title') }}" />
                        </div>
                        
                        {{-- Campo: description --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Descripción Detallada:</label>
                            <textarea name="description" rows="3"
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" placeholder="Detalle la falla, incluyendo síntomas y pasos para replicarla.">{{ $getValue('description') }}</textarea>
                        </div>
                        
                        {{-- Campo: category (Valores de la API como string) --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Categoría:</label>
                            <select name="category" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php $currentCategory = strtolower($getValue('category')); @endphp
                                <option value="" disabled {{ !$isEditing && $currentCategory == '' ? 'selected' : '' }}>Selecciona Categoría</option>
                                @foreach($categoryOptions as $value => $label)
                                    {{-- El valor enviado es la cadena (ej: 'corrective') --}}
                                    <option value="{{ $value }}" {{ (strtolower($value) === $currentCategory) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Campo: priority --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Prioridad:</label>
                            <select name="priority" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php $currentPriority = strtolower($getValue('priority')); @endphp
                                <option value="" disabled {{ !$isEditing && $currentPriority == '' ? 'selected' : '' }}>Selecciona Prioridad</option>
                                @foreach($priorityOptions as $eng => $esp)
                                    <option value="{{ $eng }}" {{ (strtolower($eng) === $currentPriority) ? 'selected' : '' }}>{{ $esp }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Campo: equipment --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Equipo Afectado:</label>
                            <input type="text" name="equipment"
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" placeholder="Ej: Impresora XYZ o Servidor-01" value="{{ $getValue('equipment') }}" />
                        </div>
                        
                        {{-- Campo: start_time --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Hora de Inicio:</label>
                            <input type="datetime-local" name="start_time" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors" value="{{ $formatDate($getValue('start_time')) }}" />
                        </div>
                        
                        {{-- Campo: end_time --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Hora de Fin:</label>
                            <input type="datetime-local" name="end_time" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors" value="{{ $formatDate($getValue('end_time')) }}" />
                        </div>
                        
                        {{-- Campo: duration (H:M:S) - Envía HH:MM:SS como string --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Duración (H:M:S):</label>
                            <div class="flex space-x-2">
                                
                                {{-- Selector de Horas --}}
                                <select id="duration_hours" 
                                    class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                    <option value="">HH</option>
                                    @foreach($hoursOptions as $h)
                                        <option value="{{ $h }}" {{ (intval($h) === intval($currentHours)) ? 'selected' : '' }}>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>

                                {{-- Selector de Minutos --}}
                                <select id="duration_minutes" 
                                    class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                    <option value="">MM</option>
                                    @foreach($minuteSecondOptions as $m)
                                        <option value="{{ $m }}" {{ (intval($m) === intval($currentMinutes)) ? 'selected' : '' }}>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>

                                {{-- Selector de Segundos --}}
                                <select id="duration_seconds" 
                                    class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                    <option value="">SS</option>
                                    @foreach($minuteSecondOptions as $s)
                                        <option value="{{ $s }}" {{ (intval($s) === intval($currentSeconds)) ? 'selected' : '' }}>{{ str_pad($s, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>
                                
                                {{-- CAMPO OCULTO REAL: Aquí se almacenará la duración total en formato HH:MM:SS --}}
                                <input type="hidden" name="duration" id="duration_hidden" value="{{ $durationString }}">
                            </div>
                        </div>
                        
                        {{-- Campo: status (SOLUCIÓN IMPLEMENTADA: Forzar 'abierto' en creación y usar valores en español) --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Estado:</label>
                            <select name="status" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors">
                                @php 
                                    // Obtener el valor actual o el valor por defecto ('abierto' en creación)
                                    $selectedStatus = strtolower($getValue('status')); 
                                @endphp
                                
                                {{-- Quitamos la opción vacía para evitar que se envíe un valor no válido --}}
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ (strtolower($value) === $selectedStatus) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Campos: company, user, location (Agrupados y requeridos) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Asignación (Cía ID | User ID | Loc ID):</label>
                            <div class="flex space-x-2">
                                <input type="number" name="company" required class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="Cía ID (Ej: 1)" value="{{ $getValue('company') }}" />
                                <input type="number" name="user" required class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="User ID (Ej: 1)" value="{{ $getValue('user') }}" />
                                {{-- Nota: Recuerda sanear este campo a 'null' en tu controlador si está vacío --}}
                                <input type="number" name="location" class="w-1/3 p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:border-ff-primary transition-colors" placeholder="Loc ID (Opcional)" value="{{ $getValue('location') }}" />
                            </div>
                        </div>

                        {{-- Campo: report --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-dark mb-1">Reporte Técnico / Observación Final:</label>
                            <textarea name="report" rows="1" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 bg-ff-bg-light focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-500" placeholder="Detalle la solución o el estado final del ticket">{{ $getValue('report') }}</textarea>
                        </div>

                    </div>

                    {{-- Botones de Enviar --}}
                    <div class="pt-4 flex justify-end space-x-4">
                        @if($isEditing)
                            <a href="{{ route('tickets.index') }}" class="w-1/3 md:w-auto bg-gray-400 text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:bg-gray-500 transition-all">
                                Cancelar Edición
                            </a>
                            <button type="submit"
                                class="w-2/3 md:w-auto bg-ff-error text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:bg-opacity-95 transition-all transform hover:scale-[1.005] flex items-center justify-center">
                                <i class="fas fa-sync-alt mr-2"></i> Actualizar Ticket #{{ $ticketToEdit['id'] }}
                            </button>
                        @else
                            <button type="submit"
                                class="w-full bg-ff-primary text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:bg-opacity-95 transition-all transform hover:scale-[1.005] flex items-center justify-center">
                                <i class="fas fa-ticket-alt mr-2"></i> Reportar Nuevo Ticket
                            </button>
                        @endif
                    </div>
                </form>
            </div>
            
            ---
            
            {{-- 🐳 Sección de Listado de Tickets --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-secondary overflow-x-auto">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-200 pb-3">
                    Listado de Tickets <span class="text-ff-secondary">| Base de Datos</span>
                </h2>

                @if(!empty($tickets) && is_array($tickets))
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-ff-secondary">
                                <tr>
                                    @php
                                        $headers = [
                                            'ID', 'Título', 'Prioridad', 'Categoría', 'Estatus', 'Cía ID', 'Usuario ID', 'Acciones'
                                        ];
                                    @endphp
                                    @foreach($headers as $header)
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-ff-white uppercase tracking-wider whitespace-nowrap">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-ff-white divide-y divide-gray-100 text-ff-dark">
                                @forelse($tickets as $ticket)
                                    <tr class="hover:bg-ff-bg-light transition duration-150">
                                        
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-ff-secondary">{{ $ticket['id'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-ff-dark">{{ $ticket['title'] ?? 'N/A' }}</td>
                                        
                                        {{-- Prioridad (Color y Etiqueta) --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $priorityValue = strtolower($ticket['priority'] ?? 'none');
                                                $priorityLabel = $priorityOptions[$priorityValue] ?? ucfirst($priorityValue);
                                                $priorityClass = $priorityValue === 'high' ? 'bg-ff-error' : 
                                                                 ($priorityValue === 'medium' ? 'bg-ff-primary' : 
                                                                 ($priorityValue === 'low' ? 'bg-green-600' : 'bg-gray-400'));
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-ff-white {{ $priorityClass }}">
                                                {{ $priorityLabel }}
                                            </span>
                                        </td>
                                        
                                        {{-- Categoría --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $categoryOptions[strtolower($ticket['category'] ?? 'none')] ?? $ticket['category'] ?? 'N/A' }}</td>
                                        
                                        {{-- Estatus (Color y Etiqueta) --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $statusValue = strtolower($ticket['status'] ?? 'en_espera');
                                                $statusLabel = $statusOptions[$statusValue] ?? ucfirst(str_replace('_', ' ', $statusValue));
                                                $statusClass = $statusValue === 'cerrado' ? 'bg-ff-success' : 
                                                               ($statusValue === 'en_curso' ? 'bg-ff-primary' : 'bg-gray-400');
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-ff-white {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $ticket['company'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $ticket['user'] ?? 'N/A' }}</td>
                                        
                                        {{-- Columna de Acciones (Editar/Eliminar) --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('tickets.index', ['edit_id' => $ticket['id']]) }}" class="text-ff-primary hover:text-ff-dark transition duration-150 font-bold mr-3 underline" >
                                                ✏️ Editar
                                            </a>
                                            
                                            <button type="button" 
                                                class="text-ff-error hover:text-ff-dark transition duration-150 font-bold underline focus:outline-none"
                                                onclick="showDeleteModal({{ $ticket['id'] }})">
                                                🗑️ Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-xl text-ff-dark font-medium">
                                            ❌ No hay tickets disponibles.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center border-2 border-dashed border-gray-300 rounded-xl bg-ff-bg-light/70">
                        <p class="text-xl text-ff-dark font-medium">❌ No hay tickets creados o hubo un problema al conectar con la API.</p>
                        <p class="text-gray-500 mt-2">Revisa tu conexión a la API y el controlador.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --}}
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden z-50 transition-opacity duration-300 ease-out" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="bg-ff-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform transition-all sm:my-8 sm:align-middle">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-ff-error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.38 18c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-ff-dark" id="modal-title">
                            Confirmar Eliminación de Ticket
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                ¿Estás seguro de que deseas eliminar el Ticket #<span id="ticketIdDisplay" class="font-bold text-ff-dark"></span>? Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    {{-- Formulario Oculto para DELETE --}}
                    <form id="deleteForm" method="POST" action="" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-ff-error text-base font-medium text-ff-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Sí, Eliminar
                        </button>
                    </form>
                    
                    <button type="button" onclick="hideDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition duration-150">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Lógica de JavaScript para el Modal de Eliminación
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const ticketIdDisplay = document.getElementById('ticketIdDisplay');

        function showDeleteModal(ticketId) {
            // Genera la URL para la ruta 'tickets.delete' de Laravel con el ID
            const laravelDestroyRoute = "{{ route('tickets.delete', ['id' => 'TEMP_ID']) }}";
            deleteForm.action = laravelDestroyRoute.replace('TEMP_ID', ticketId);
            ticketIdDisplay.textContent = ticketId;
            deleteModal.classList.remove('hidden');
        }

        function hideDeleteModal() {
            deleteModal.classList.add('hidden');
        }

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                hideDeleteModal();
            }
        });
        
        // LÓGICA PARA CALCULAR Y FORMATEAR LA DURACIÓN A HH:MM:SS
        const form = document.querySelector('form');
        const durationHours = document.getElementById('duration_hours');
        const durationMinutes = document.getElementById('duration_minutes');
        const durationSeconds = document.getElementById('duration_seconds');
        const durationHidden = document.getElementById('duration_hidden');

        // Función para formatear cualquier número a dos dígitos (ej: 5 -> 05)
        const pad = (num) => String(num).padStart(2, '0');

        // Función para calcular la duración total y actualizar el campo oculto
        function calculateAndFormatDuration() {
            // Obtener valores, usando 0 si el selector está vacío
            const hours = parseInt(durationHours.value) || 0;
            const minutes = parseInt(durationMinutes.value) || 0;
            const seconds = parseInt(durationSeconds.value) || 0;
            
            // Formatear al estándar ISO 8601 (HH:MM:SS)
            const formattedDuration = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

            // Actualizar el campo oculto que se enviará al servidor (el que Laravel valida)
            durationHidden.value = formattedDuration;
        }

        // Ejecutar el cálculo justo antes de que el formulario se envíe
        if (form) {
            form.addEventListener('submit', calculateAndFormatDuration);
        }

        // Ejecutar el cálculo cuando el usuario interactúe (para actualizar el valor oculto dinámicamente)
        if (durationHours) {
            durationHours.addEventListener('change', calculateAndFormatDuration);
            durationMinutes.addEventListener('change', calculateAndFormatDuration);
            durationSeconds.addEventListener('change', calculateAndFormatDuration);
        }
        
        // Cargar Font Awesome para iconos (Si no está cargado ya)
        if (typeof FontAwesome === 'undefined') {
            const faScript = document.createElement('script');
            faScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js';
            faScript.crossOrigin = 'anonymous';
            document.head.appendChild(faScript);
        }
    </script>
</x-app-layout>