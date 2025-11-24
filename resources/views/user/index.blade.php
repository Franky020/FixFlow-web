<x-app-layout>

    @php
        // Modo crear / editar
        $editingUserId = request('edit');
        $isEditing = !empty($editingUserId);

        // Cargar datos del usuario si está editando
        $userToEdit = [];
        if ($isEditing && !empty($data)) {
            $userToEdit = collect($data)->firstWhere('id', $editingUserId) ?? [];
        }

        // Título y acción del formulario
        $usernameToEdit = $userToEdit['username'] ?? 'Usuario Desconocido';
        $formTitle = $isEditing
            ? "Editar Usuario ID: {$editingUserId} | {$usernameToEdit}"
            : 'Crear Nuevo Usuario | Detallado';

        $formRoute = $isEditing
            ? route('user.update', $editingUserId)
            : route('user.create');

        $buttonText = $isEditing
            ? '<i class="fas fa-save mr-2"></i> Guardar Cambios'
            : '<i class="fas fa-user-plus mr-2"></i> Crear Usuario';

        $formButtonClass = $isEditing
            ? 'bg-ff-success ring-ff-success/50 hover:ring-ff-success'
            : 'bg-ff-primary ring-ff-primary/50 hover:ring-ff-primary';

        // Campos
        $fields = [
            ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],

            ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password',
             'required' => true, 'placeholder' => 'Reingrese para confirmar'],

            ['name' => 'first_name', 'label' => 'Primer Nombre', 'type' => 'text', 'required' => true],
            ['name' => 'last_name', 'label' => 'Apellido', 'type' => 'text', 'required' => true],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text', 'required' => true],

            ['name' => 'user_type', 'label' => 'Tipo de Usuario', 'type' => 'select',
             'options' => ['Super Admin', 'Admin', 'Normal User'], 'required' => true],

            ['name' => 'age', 'label' => 'Edad', 'type' => 'number', 'required' => true],
            ['name' => 'status', 'label' => 'Estatus', 'type' => 'select',
             'options' => ['Activo', 'Inactivo', 'Pendiente'], 'required' => true],

            ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'required' => true],
            ['name' => 'rfc', 'label' => 'RFC', 'type' => 'text', 'required' => true],

            ['name' => 'company', 'label' => 'Compañía', 'type' => 'company_select', 'required' => true],

            // ⚠ CAMBIO: Este campo AHORA será un input file real
            ['name' => 'photo', 'label' => 'Foto del Usuario', 'type' => 'file', 'required' => false],
        ];
    @endphp

    <x-slot name="header">
        <div class="border-l-4 border-ff-primary pl-4">
            <h2 class="font-bold text-2xl text-ff-dark leading-tight">
                Gestión de Cuentas y Detalles de Usuario
            </h2>
        </div>
    </x-slot>


    <div class="py-12 bg-ff-bg-light min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Éxito --}}
            @if (session('success'))
            <div class="bg-ff-success text-ff-white p-4 rounded-lg shadow-lg mb-6 font-semibold flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                {{ session('success') }}
            </div>
            @endif

            {{-- Error API --}}
            @error('api_error')
            <div class="bg-ff-error text-ff-white p-4 rounded-lg shadow-lg mb-6 font-semibold flex items-center">
                <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
                Error de Conexión: {{ $message }}
            </div>
            @enderror


            {{-- FORMULARIO --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-primary mb-12">
                <h2 class="text-3xl font-extrabold text-ff-dark mb-6 border-b-2 pb-3">
                    {{ $formTitle }}
                    <span class="text-ff-primary">| {{ $isEditing ? 'Edición' : 'Creación' }}</span>
                </h2>

                {{-- Errores --}}
                @if ($errors->any() && !$errors->has('api_error'))
                <div class="bg-ff-error/10 border border-ff-error text-ff-error p-4 rounded-lg mb-6">
                    <p class="font-bold mb-2">
                        Corrige los siguientes errores:
                    </p>
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif


                {{-- FORM REAL CON FILE SUPPORT --}}
                <form method="POST" action="{{ $formRoute }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if ($isEditing) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                {{ $field['label'] }}:
                                @if($field['required']) <span class="text-ff-primary">*</span> @endif

                                @if ($field['name'] === 'password' && $isEditing)
                                <span class="text-ff-error text-xs">(Debe reingresarla)</span>
                                @endif
                            </label>

                            @php
                                $currentValue = old($field['name']);

                                if ($isEditing && empty($currentValue) && $field['name'] !== 'password') {
                                    if ($field['name'] === 'company') {
                                        $currentValue = $userToEdit['company']['id'] ?? '';
                                    } elseif ($field['name'] !== 'photo') {
                                        $currentValue = $userToEdit[$field['name']] ?? '';
                                    }
                                }
                            @endphp

                            {{-- Input file especial --}}
                            @if ($field['type'] === 'file')

                                <input
                                    type="file"
                                    name="photo"
                                    accept="image/*"
                                    class="w-full p-3 rounded-lg border-2 border-gray-300">

                                {{-- Preview existente --}}
                                @if ($isEditing && !empty($userToEdit['photo']))
                                <img src="{{ $userToEdit['photo'] }}"
                                     class="mt-3 w-24 h-24 rounded-lg border shadow">
                                @endif

                                @error('photo')
                                <p class="text-xs text-ff-error mt-1 italic">{{ $message }}</p>
                                @enderror

                            {{-- Select estáticos --}}
                            @elseif ($field['type'] === 'select')
                                <select
                                    name="{{ $field['name'] }}"
                                    required="{{ $field['required'] }}"
                                    class="w-full p-3 rounded-lg border-2 border-gray-300">
                                    <option value="" disabled>Seleccionar</option>
                                    @foreach ($field['options'] as $opt)
                                    <option value="{{ $opt }}" {{ $currentValue == $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                    @endforeach
                                </select>

                            {{-- Select compañía --}}
                            @elseif ($field['type'] === 'company_select')
                                <select name="company"
                                        class="w-full p-3 rounded-lg border-2 border-gray-300">
                                    <option value="" disabled>Seleccionar Compañía</option>
                                    @foreach ($companies as $comp)
                                    <option value="{{ $comp['id'] }}"
                                        {{ $currentValue == $comp['id'] ? 'selected' : '' }}>
                                        {{ $comp['name'] }} (ID {{ $comp['id'] }})
                                    </option>
                                    @endforeach
                                </select>

                            {{-- Input estándar --}}
                            @else
                                <input
                                    type="{{ $field['type'] }}"
                                    name="{{ $field['name'] }}"
                                    value="{{ $field['type'] === 'password' ? '' : $currentValue }}"
                                    required="{{ $field['required'] }}"
                                    class="w-full p-3 rounded-lg border-2 border-gray-300"
                                    placeholder="{{ $field['placeholder'] ?? '' }}">
                            @endif

                        </div>
                        @endforeach
                    </div>

                    {{-- BOTONES --}}
                    <div class="pt-4 flex {{ $isEditing ? 'justify-between' : 'justify-end' }}">
                        @if ($isEditing)
                        <a href="{{ route('user.list') }}"
                           class="bg-gray-500 text-white px-6 py-3 rounded-xl font-bold shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i> Cancelar
                        </a>
                        @endif

                        <button type="submit"
                            class="px-6 py-3 rounded-xl font-bold shadow-lg text-white ring-2 {{ $formButtonClass }}">
                            {!! $buttonText !!}
                        </button>
                    </div>

                </form>

            </div>


            {{-- LISTA DE USUARIOS --}}
            <div class="bg-ff-white shadow-2xl rounded-xl p-6 lg:p-8 border-t-8 border-ff-secondary overflow-x-auto">

                <h2 class="text-3xl font-extrabold mb-6 border-b-2 pb-3">
                    Listado Completo de Usuarios 
                </h2>

                @if(!empty($data))
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-ff-secondary">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-white">Foto</th>
                            @php
                                $headers = ['ID','Usuario','Email','Nombre','Apellido','Tipo','Estado','Edad','Compañía','Teléfono','Dirección','RFC'];
                            @endphp
                            @foreach($headers as $h)
                            <th class="px-6 py-3 text-left text-xs font-bold text-white">{{ $h }}</th>
                            @endforeach
                            <th class="px-6 py-3 text-left text-xs font-bold text-white">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data as $user)
                        <tr class="hover:bg-gray-100">

                            {{-- FOTO --}}
                            <td class="px-6 py-4">
                                @if (!empty($user['photo']))
                                    <img src="{{ $user['photo'] }}" class="w-12 h-12 rounded-lg shadow">
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>

                            {{-- RESTO DE CAMPOS --}}
                            <td class="px-6 py-4">{{ $user['id'] }}</td>
                            <td class="px-6 py-4 font-bold">{{ $user['username'] }}</td>
                            <td class="px-6 py-4">{{ $user['email'] }}</td>
                            <td class="px-6 py-4">{{ $user['first_name'] }}</td>
                            <td class="px-6 py-4">{{ $user['last_name'] }}</td>
                            <td class="px-6 py-4">{{ $user['user_type'] }}</td>
                            <td class="px-6 py-4">{{ $user['status'] }}</td>
                            <td class="px-6 py-4">{{ $user['age'] }}</td>

                            <td class="px-6 py-4"
    data-company-id="{{ is_array($user['company'] ?? null) ? ($user['company']['id'] ?? '') : ($user['company'] ?? '') }}">
    
    {{ is_array($user['company'] ?? null)
        ? ($user['company']['name'] ?? 'N/A')
        : ($user['company'] ?? 'N/A') }}
</td>


                            <td class="px-6 py-4">{{ $user['phone'] }}</td>
                            <td class="px-6 py-4">{{ $user['address'] }}</td>
                            <td class="px-6 py-4">{{ $user['rfc'] }}</td>

                            <td class="px-6 py-4">

                                {{-- EDITAR --}}
                                <a href="{{ route('user.list', ['edit' => $user['id']]) }}"
                                   class="text-ff-primary font-bold underline">
                                    Editar
                                </a>

                                {{-- ELIMINAR --}}
                                <form action="{{ route('user.delete', $user['id']) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('¿Eliminar usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-ff-error font-bold underline ml-3">
                                        Eliminar
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                @else
                <p class="text-center text-gray-500 py-10">
                    No hay usuarios disponibles.
                </p>
                @endif

            </div>
        </div>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const role = localStorage.getItem("role");
    const sessionCompanyId = Number(localStorage.getItem("company")); 
    const userData = JSON.parse(localStorage.getItem("user_data") || "{}");

    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const userId = Number(row.querySelector("td:nth-child(2)").innerText.trim()); // Busca el ID en la segunda columna (index 1 + 1)
        
        // 1. Obtener la celda de compañía usando el atributo data-company-id
        const companyCell = row.querySelector("td[data-company-id]");
        const userCompanyId = companyCell ? Number(companyCell.dataset.companyId) : 0;
        
        let visible = false; // Valor inicial más seguro

        if (role === "super_admin" || role === "Super Admin") {
            visible = true; // Super Admin ve a todos
        }
        
        // El tipo de usuario en el JWT es "super_admin", ajusta la comparación
        if (role === "admin" || role === "Admin") {
            // ADMIN solo ve usuarios con el mismo ID de compañía
            visible = userCompanyId === sessionCompanyId;
        }

        if (role === "normal_user" || role === "Normal User") {
            // Usuario Normal solo se ve a sí mismo
            visible = userId === userData.id;
        }

        if (!visible) {
            row.style.display = "none";
        }
    });

});
</script>



</x-app-layout>
