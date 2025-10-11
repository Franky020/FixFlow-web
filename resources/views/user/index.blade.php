<x-app-layout>
    {{-- Encabezado de la página usando el slot 'header' de Breeze --}}
    <x-slot name="header">
        {{-- Borde primario (Naranja) en el encabezado --}}
        <div class="border-l-4 border-ff-primary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Gestión de Cuentas y Detalles de Usuario') }}
            </h2>
        </div>
    </x-slot>

    {{-- Fondo Gris Claro de FixFlow --}}
    <div class="py-12 bg-ff-bg-light min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🧡 SECCIÓN 1: Formulario de Creación (Foco Naranja: ff-primary) --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-primary mb-12">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-100 pb-3">
                    Crear Nuevo Usuario 
                </h2>
                
                <form method="POST" action="{{ route('user.create') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        @php
                            // Definición de campos para iteración
                            $fields = [
                                ['name' => 'first_name', 'label' => 'Primer Nombre', 'type' => 'text', 'placeholder' => 'Ej: Juan'],
                                ['name' => 'last_name', 'label' => 'Apellido', 'type' => 'text', 'placeholder' => 'Ej: Pérez'],
                                ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text', 'placeholder' => 'Ej: 5551234567'],
                                ['name' => 'user_type', 'label' => 'Tipo de Usuario', 'type' => 'text', 'placeholder' => 'Ej: Técnico o Cliente'],
                                ['name' => 'age', 'label' => 'Edad', 'type' => 'number', 'placeholder' => 'Ej: 35'],
                                ['name' => 'rfc', 'label' => 'RFC', 'type' => 'text', 'placeholder' => 'Ej: ABCD010101XYZ'],
                                ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'placeholder' => 'Ej: Calle 1 #23 Col. Centro'],
                                ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password', 'placeholder' => '********'],
                                ['name' => 'company', 'label' => 'ID de Compañía', 'type' => 'number', 'placeholder' => 'Ej: 101'],
                            ];
                        @endphp

                        @foreach($fields as $field)
                            <div>
                                <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                    {{ $field['label'] }}:
                                    {{-- Detalle extra en naranja para el asterisco de requerido --}}
                                    <span class="text-ff-primary">*</span>
                                </label>
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" required 
                                    class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-400" 
                                    placeholder="{{ $field['placeholder'] }}" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Botón de Enviar (ff-primary) --}}
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-ff-primary text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-2xl hover:opacity-95 transition-all transform hover:scale-[1.005] ring-2 ring-ff-primary/50 hover:ring-ff-primary">
                            <i class="fas fa-user-plus mr-2"></i> Crear Usuario Ahora
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- 🐳 SECCIÓN 2: Listado de Usuarios (Foco Azul Petróleo: ff-secondary) --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-secondary overflow-x-auto">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-100 pb-3">
                    Listado de Usuarios 
                </h2>

                @if(!empty($data) && is_array($data))
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-ff-secondary">
                            <tr>
                                @php
                                    $headers = [
                                        'ID', 'Nombre', 'Apellido', 'Teléfono', 'Dirección', 'Tipo', 
                                        'Edad', 'RFC', 'Estado', 'Compañía'
                                    ];
                                @endphp
                                @foreach($headers as $header)
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-ff-white uppercase tracking-wider border-r border-ff-primary/50 last:border-r-0">
                                        {{ $header }}
                                    </th>
                                @endforeach
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-ff-white uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-ff-white divide-y divide-gray-100 text-ff-dark border-b-4 border-ff-primary">
                            @foreach($data as $index => $user)
                                {{-- Alternar color de fila y hover sutilmente naranja --}}
                                <tr class="{{ $index % 2 == 0 ? 'bg-ff-white' : 'bg-ff-bg-light/50' }} hover:bg-ff-primary/20 transition duration-150">
                                    
                                    @foreach(['id', 'first_name', 'last_name', 'phone', 'address', 'user_type', 'age', 'rfc', 'status', 'company'] as $key)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($key === 'status')
                                                {{-- 🚦 Estilos para el campo Status usando ff-success y ff-error --}}
                                                @php
                                                    $status = $user[$key] ?? 'N/A';
                                                    $statusClass = $status === 'Activo' ? 'bg-ff-success' : 
                                                                   ($status === 'Inactivo' ? 'bg-ff-error' : 'bg-gray-400');
                                                @endphp
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-ff-white {{ $statusClass }}">
                                                    {{ $status }}
                                                </span>
                                            @else
                                                {{ $user[$key] ?? 'N/A' }}
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    {{-- Columna de Acciones --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-ff-primary hover:text-ff-dark transition duration-150 font-bold mr-3 underline hover:no-underline">
                                            Editar
                                        </a>
                                        <a href="#" class="text-ff-error hover:text-ff-dark transition duration-150 font-bold underline hover:no-underline">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center border-2 border-dashed border-ff-primary rounded-xl bg-ff-bg-light/70">
                        <p class="text-xl text-ff-dark font-medium">❌ La lista de usuarios está vacía o hubo un problema al conectar con la API.</p>
                        <p class="text-gray-500 mt-2">Verifica la variable `API_URL` en tu `.env` y la función `getUsers()` en el controlador.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>