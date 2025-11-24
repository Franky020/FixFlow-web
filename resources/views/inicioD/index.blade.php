<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-[#F4A300] pl-4">
            <h2 class="text-3xl font-bold text-[#006D77]">
                {{ __('Panel de Control') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">

        <!-- LOADING -->
        <div id="loading"
            class="text-center py-10 text-xl font-semibold text-gray-500 bg-white shadow-lg rounded-xl">
            Cargando información del usuario...
        </div>

        <!-- CONTENIDO -->
        <div id="userContent" class="hidden">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- PANEL IZQUIERDO -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 flex flex-col items-center text-center">

                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-[#006D77] shadow-md mb-4">
                        <img id="userPhoto"
                            src="{{ session('user_data.photo') ? 'https://fixflow-endpoints.onrender.com'.session('user_data.photo') : '/img/default-avatar.png' }}"
                            alt="Foto de perfil" class="w-full h-full object-cover">
                    </div>

                    <h2 id="userName" class="text-2xl font-bold text-[#003F4E]">
                        {{ session('user_data.first_name') }} {{ session('user_data.last_name') }}
                    </h2>

                    <p id="userRole" class="text-sm text-gray-500 mt-1">
                        Rol: {{ session('user_data.user_type') }}
                    </p>

                </div>

                <!-- PANEL DERECHO -->
                <div class="md:col-span-2 space-y-8">

                    <!-- Bienvenida -->
                    <div class="bg-[#006D77] text-white p-10 rounded-2xl shadow-lg">
                        <h3 class="text-3xl font-bold mb-2">¡Bienvenido a FixFlow! 👋</h3>
                        <p id="welcomeText" class="text-lg opacity-90">
                            Hola {{ session('user_data.first_name') }}, nos alegra tenerte de vuelta.
                        </p>
                    </div>

                    <!-- Empresa Vinculada -->
                    <div class="bg-white p-8 shadow-lg rounded-2xl border border-gray-100">

                        <div class="flex items-center mb-4">
                            <span class="text-3xl text-[#006D77] mr-3">🏢</span>
                            <h3 class="text-2xl font-semibold text-[#006D77]">Empresa Vinculada</h3>
                        </div>

                        <p class="text-lg">
                            <span class="font-semibold text-gray-600">ID de Empresa:</span>
                            <span id="companyName" class="text-[#003F4E] ml-1">
                                {{ session('user_data.company') ?? 'No asignada' }}
                            </span>
                        </p>
                    </div>

                    <!-- Gráfica -->
                    <div class="bg-white p-10 shadow-lg rounded-2xl border border-gray-100 text-center">

                        <h3 class="text-2xl font-bold text-[#003F4E] mb-6">Tickets Activos</h3>

                        <div class="mx-auto rounded-full overflow-hidden flex items-center justify-center"
                            style="width:210px; height:210px;">
                            <canvas id="ticketsChart" width="210" height="210"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            document.getElementById("loading").classList.add("hidden");
            document.getElementById("userContent").classList.remove("hidden");

            setTimeout(() => {
                const canvas = document.getElementById('ticketsChart');

                new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Activos', 'Cerrados'],
                        datasets: [{
                            data: [14, 9],
                            backgroundColor: ['#006D77', '#F4A300'],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        layout: { padding: 0 }
                    }
                });

            }, 100);
        });
    </script>

</x-app-layout>
