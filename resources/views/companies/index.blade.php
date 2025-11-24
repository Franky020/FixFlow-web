<x-app-layout>
    {{-- Encabezado de la página --}}
    <x-slot name="header">
        <div class="border-l-4 border-ff-primary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                {{ __('Gestión de Compañías (Empresas Clientes)') }}
            </h2>
        </div>
    </x-slot>

    {{-- Fondo Gris Claro de FixFlow --}}
    <div class="py-12 bg-ff-bg-light min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Sesión (Éxito/Error/Info) --}}
            @if (session('success'))
                <div class="bg-ff-success/10 border-l-4 border-ff-success text-ff-success p-4 mb-4" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-ff-error/10 border-l-4 border-ff-error text-ff-error p-4 mb-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Información</p>
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            {{-- 🧡 SECCIÓN 1: Formulario Único (Creación/Edición) --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-primary mb-12">
                
                {{-- Determinar Título y Acción del Formulario --}}
                @php
                    $isEditing = $companyToEdit !== null;
                    $formTitle = $isEditing 
                                 ? "Editar Compañía #{$companyToEdit['id']} <span class='text-ff-primary'>| Actualizando datos</span>"
                                 : "Registrar Nueva Compañía <span class='text-ff-primary'>| Detalle Básico</span>";
                    $formAction = $isEditing
                                  ? route('company.update', ['id' => $companyToEdit['id']])
                                  : route('company.create');
                    $buttonText = $isEditing ? 'Guardar Cambios' : 'Registrar Compañía';
                    $currentData = $companyToEdit ?? []; 
                    
                    // Mapeo para PLANES (Español => Inglés/API Format - minúsculas, guion bajo)
                    $plan_options_map = [
                        'Básico' => 'basic', 
                        'Premium' => 'premium', 
                        'Empresarial' => 'enterprise', 
                        'Personalizado' => 'custom',
                        'Interno FixFlow' => 'fixflow_internal', 
                    ]; 
                    
                    // Mapeo para ESTADO (Español => Inglés/API Format - minúsculas)
                    $status_options_map = [
                        'Activo' => 'active', 
                        'Inactivo' => 'inactive'
                    ]; 
                @endphp

                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-100 pb-3">
                    {!! $formTitle !!}
                </h2>
                
                <form method="POST" action="{{ $formAction }}" class="space-y-6">
                    @csrf
                    
                    @if ($isEditing)
                        @method('PUT') {{-- NECESARIO para el método UPDATE --}}
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        @php
                            // Campos de INPUT (Texto)
                            $text_fields = [
                                ['name' => 'name', 'label' => 'Nombre de la Compañía', 'placeholder' => 'Ej: Soluciones Globales S.A.', 'required' => true],
                                ['name' => 'contact', 'label' => 'Contacto Principal', 'placeholder' => 'Ej: 55-8765-4321', 'required' => true],
                                ['name' => 'address', 'label' => 'Dirección Completa', 'placeholder' => 'Ej: Av. Central #100 Col. Industrial', 'required' => true],
                                ['name' => 'logo', 'label' => 'URL del Logo', 'placeholder' => 'https://ruta/logo.png', 'required' => false],
                            ];
                        @endphp

                        {{-- Campos de Texto (Nombre, Contacto) --}}
                        @foreach(array_slice($text_fields, 0, 2) as $field)
                            <div>
                                <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                    {{ $field['label'] }}: <span class="text-ff-primary">*</span>
                                </label>
                                <input type="text" name="{{ $field['name'] }}" {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-400" 
                                    placeholder="{{ $field['placeholder'] }}" 
                                    value="{{ old($field['name'], $currentData[$field['name']] ?? '') }}" />
                                @error($field['name']) <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                        
                        {{-- SELECT: Tipo de Plan (Muestra español, envía valor API) --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                Tipo de Plan: <span class="text-ff-primary">*</span>
                            </label>
                            <select name="plan_type" required
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors">
                                <option value="" disabled {{ !old('plan_type', $currentData['plan_type'] ?? '') ? 'selected' : '' }}>Selecciona un tipo de plan</option>
                                {{-- Usamos $label (Español) para mostrar, pero $value (Inglés) para enviar --}}
                                @foreach($plan_options_map as $label => $value)
                                    <option value="{{ $value }}" 
                                        {{ old('plan_type', $currentData['plan_type'] ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_type') <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- Descripción (Ocupa las 3 columnas) --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                Descripción de la Compañía: <span class="text-ff-primary">*</span>
                            </label>
                            <textarea name="description" rows="3" required
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-400" 
                                placeholder="Breve resumen de la compañía, rubro o notas importantes.">{{ old('description', $currentData['description'] ?? '') }}</textarea>
                            @error('description') <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- Input: Dirección Completa --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                {{ $text_fields[2]['label'] }}: <span class="text-ff-primary">*</span>
                            </label>
                            <input type="text" name="address" required 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-400" 
                                placeholder="{{ $text_fields[2]['placeholder'] }}" 
                                value="{{ old('address', $currentData['address'] ?? '') }}" />
                            @error('address') <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- Input: URL Logo --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                {{ $text_fields[3]['label'] }}:
                            </label>
                            <input type="text" name="logo" 
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors placeholder-gray-400" 
                                placeholder="{{ $text_fields[3]['placeholder'] }}" 
                                value="{{ old('logo', $currentData['logo'] ?? '') }}" />
                            @error('logo') <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- SELECT: Estado Inicial (Muestra español, envía valor API) --}}
                        <div>
                            <label class="block text-sm font-semibold text-ff-secondary mb-1">
                                Estado Inicial: <span class="text-ff-primary">*</span>
                            </label>
                            <select name="status" required
                                class="w-full p-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-ff-primary transition-colors">
                                <option value="" disabled {{ !old('status', $currentData['status'] ?? '') ? 'selected' : '' }}>Selecciona un estado</option>
                                {{-- Usamos $label (Español) para mostrar, pero $value (Inglés) para enviar --}}
                                @foreach($status_options_map as $label => $value)
                                    <option value="{{ $value }}" 
                                        {{ old('status', $currentData['status'] ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-ff-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                    </div>

                    {{-- Botón de Enviar --}}
                    <div class="pt-4 md:col-span-3 flex items-center space-x-4">
                        <button type="submit"
                            class="flex-1 bg-ff-primary text-ff-white px-6 py-3 rounded-xl font-bold text-lg shadow-lg hover:shadow-2xl hover:opacity-95 transition-all transform hover:scale-[1.005] ring-2 ring-ff-primary/50 hover:ring-ff-primary">
                            <i class="fas fa-building mr-2"></i> {{ $buttonText }}
                        </button>
                        
                        @if ($isEditing)
                            {{-- Botón de Cancelar Edición --}}
                            <a href="{{ route('companies.index') }}" 
                               class="px-6 py-3 rounded-xl font-bold text-lg text-ff-dark bg-gray-200 hover:bg-gray-300 transition-colors">
                                Cancelar Edición
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            ---
            
            {{-- 🐳 SECCIÓN 2: Listado de Compañías (Traducción Inversa para Visualización) --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-secondary overflow-x-auto">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 border-gray-100 pb-3">
                    Listado de Compañías Registradas <span class="text-ff-secondary">| API</span>
                </h2>

                @if(!empty($data) && is_array($data))
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-ff-secondary">
                            <tr>
                                @php
                                    $headers = ['ID', 'Nombre', 'Plan', 'Contacto', 'Descripción', 'Dirección', 'Logo', 'Estado'];
                                    $dataKeys = ['id', 'name', 'plan_type', 'contact', 'description', 'address', 'logo', 'status']; 
                                    
                                    // Mapeo inverso de INGLÉS (API) a ESPAÑOL (Visual)
                                    $status_display_map = array_flip($status_options_map); 
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
                            @foreach($data as $index => $company)
                                <tr class="{{ $index % 2 == 0 ? 'bg-ff-white' : 'bg-ff-bg-light/50' }} hover:bg-ff-primary/20 transition duration-150">
                                    
                                    @foreach($dataKeys as $key)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $key === 'name' ? 'font-bold text-ff-dark' : '' }} {{ $key === 'description' ? 'whitespace-normal max-w-xs' : '' }}">
                                            @if($key === 'status')
                                                @php
                                                    // Mapeamos el estado de INGLÉS (API) a ESPAÑOL (Visual)
                                                    $statusApi = strtolower($company[$key] ?? 'N/A'); // Asegurar minúsculas
                                                    $statusDisplay = $status_display_map[$statusApi] ?? $statusApi; 
                                                    
                                                    $statusClass = $statusApi === 'active' ? 'bg-ff-success' : 
                                                                   ($statusApi === 'inactive' ? 'bg-ff-error' : 'bg-gray-400');
                                                @endphp
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-ff-white {{ $statusClass }}">
                                                    {{ $statusDisplay }}
                                                </span>
                                            @elseif($key === 'logo')
                                                <a href="{{ $company[$key] ?? '#' }}" target="_blank" class="text-ff-secondary hover:text-ff-primary underline">Ver Logo</a>
                                            @else
                                                {{-- Mapeamos el plan de INGLÉS (API) a ESPAÑOL (Visual) --}}
                                                @if ($key === 'plan_type')
                                                    @php
                                                        $planApi = strtolower($company[$key] ?? 'N/A'); // Asegurar minúsculas
                                                        // array_search busca el valor (Inglés) y devuelve la clave (Español)
                                                        $planDisplay = array_search($planApi, $plan_options_map) ?: $planApi;
                                                    @endphp
                                                    {{ $planDisplay }}
                                                @else
                                                    {{ $company[$key] ?? 'N/A' }}
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    {{-- Columna de Acciones --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">
                                        
                                        {{-- El botón "Editar" redirige al index con el parámetro edit_id --}}
                                        <a href="{{ route('companies.index', ['edit_id' => $company['id']]) }}" 
                                           class="text-ff-primary hover:text-ff-dark transition duration-150 font-bold underline hover:no-underline">
                                            Editar
                                        </a>

                                        {{-- Formulario para Eliminar --}}
                                        <form method="POST" action="{{ route('company.destroy', ['id' => $company['id']]) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar la compañía: {{ $company['name'] ?? 'N/A' }}?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-ff-error hover:text-ff-dark transition duration-150 font-bold underline hover:no-underline bg-transparent border-none p-0 cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- Mensaje Provisional --}}
                    <div class="p-8 text-center border-2 border-dashed border-ff-primary rounded-xl bg-ff-bg-light/70">
                        <p class="text-xl text-ff-dark font-medium">✨ **No hay compañías registradas en este momento.**</p>
                        <p class="text-gray-500 mt-2">Utiliza el formulario superior para añadir una nueva compañía. Los datos aparecerán aquí.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>