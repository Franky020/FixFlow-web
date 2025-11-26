<x-app-layout>
    <x-slot name="header">
        <div class="border-l-4 border-[#F4A300] pl-4">
            <h2 class="text-3xl font-bold text-[#006D77]">
                {{ __('Panel de Control') }}
            </h2>
        </div>
    </x-slot>

    @php
        $role = session('user_data.user_type') ?? null;
        $token = session('access_token');
    @endphp

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
                <div
                    class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 flex flex-col items-center text-center">

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
                        <p class="text-lg opacity-90">
                            Hola {{ session('user_data.first_name') }}, nos alegra tenerte de vuelta.
                        </p>
                    </div>

                    <!-- Empresa -->
                    <div class="bg-white p-8 shadow-lg rounded-2xl border border-gray-100">
                        <div class="flex items-center mb-4">
                            <span class="text-3xl text-[#006D77] mr-3">🏢</span>
                            <h3 class="text-2xl font-semibold text-[#006D77]">Empresa Vinculada</h3>
                        </div>

                        <p class="text-lg">
                            <span class="font-semibold text-gray-600">ID de Empresa:</span>
                            <span class="text-[#003F4E] ml-1">
                                {{ session('user_data.company') ?? 'No asignada' }}
                            </span>
                        </p>
                    </div>

                    <!-- ================= NO ADMIN ================= -->
                    @if ($role !== 'admin')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- PLANES -->
                        <div class="bg-white p-6 shadow-lg rounded-2xl border border-gray-100 text-center">
                            <h3 class="text-xl font-bold text-[#003F4E] mb-6">
                                Planes Activos
                            </h3>
                            <div class="mx-auto" style="width:240px; height:240px;">
                                <canvas id="ticketsChart"></canvas>
                            </div>
                        </div>

                        <!-- ESTADO EMPRESAS -->
                        <div class="bg-white p-6 shadow-lg rounded-2xl border border-gray-100 text-center">
                            <h3 class="text-xl font-bold text-[#003F4E] mb-6">
                                Estado de Empresas
                            </h3>
                            <div class="mx-auto" style="width:240px; height:240px;">
                                <canvas id="companiesChart"></canvas>
                            </div>
                        </div>

                    </div>

                    <!-- TICKETS POR COMPAÑÍA -->
                    <div class="bg-white p-8 shadow-lg rounded-2xl border border-gray-100 mt-8">
                        <h3 class="text-2xl font-bold text-[#003F4E] mb-6 text-center">
                            Tickets por compañía
                        </h3>
                        <div class="mx-auto" style="width:100%; max-width:800px;">
                            <canvas id="ticketsStatusChart"></canvas>
                        </div>
                    </div>
                    @endif

                    <!-- ================= ADMIN ================= -->
                    @if ($role === 'admin')

                    <!-- ROLES -->
                    <div class="bg-white p-8 shadow-lg rounded-2xl border border-gray-100 mt-8">
                        <h3 class="text-2xl font-bold text-[#003F4E] mb-6 text-center">
                            Usuarios por Rol y Compañía
                        </h3>
                        <div class="mx-auto" style="width:100%; max-width:600px;">
                            <canvas id="rolesChart"></canvas>
                        </div>
                    </div>

                    @endif

                </div>
            </div>
        </div>
    </div>

<script>
/* =========================================
   FUNCIÓN GLOBAL CON TOKEN ✅
==========================================*/
function fetchWithToken(url) {

    const token = "{{ $token }}";

    if (!token) {
        console.error("❌ NO HAY TOKEN DISPONIBLE");
        return Promise.reject("Sin token");
    }

    return fetch(url, {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json",
            "Content-Type": "application/json"
        }
    });
}

/* =========================================
   DOM READY
==========================================*/
document.addEventListener("DOMContentLoaded", () => {

    document.getElementById("loading")?.classList.add("hidden");
    document.getElementById("userContent")?.classList.remove("hidden");

    const role = "{{ $role }}";

    /* =========================================
       USUARIO NO ADMIN
    ==========================================*/
    if (role !== "admin") {

        /* EMPRESAS + PLANES */
        fetchWithToken("/companies-stats")
        .then(res => res.json())
        .then(data => {

            const canvas1 = document.getElementById('ticketsChart');

            if (canvas1 && data.active_plan_breakdown) {

                if (canvas1.chartInstance) {
                    canvas1.chartInstance.destroy();
                }

                canvas1.chartInstance = new Chart(canvas1, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data.active_plan_breakdown),
                        datasets: [{
                            data: Object.values(data.active_plan_breakdown),
                            backgroundColor: ['#006D77', '#F4A300', '#9B5DE5'],
                        }]
                    }
                });
            }

            const canvas2 = document.getElementById('companiesChart');

            if (canvas2 && data.status_summary) {

                if (canvas2.chartInstance) {
                    canvas2.chartInstance.destroy();
                }

                canvas2.chartInstance = new Chart(canvas2, {
                    type: 'doughnut',
                    data: {
                        labels: ["Activas", "Inactivas"],
                        datasets: [{
                            data: [
                                data.status_summary.active || 0,
                                data.status_summary.inactive || 0
                            ],
                            backgroundColor: ['#198754', '#DC3545']
                        }]
                    }
                });
            }

        }).catch(err => console.error("❌ Error companies-stats:", err));


        /* TICKETS POR ESTADO + COMPAÑÍA */
        fetchWithToken("/tickets-status-stats")
        .then(res => res.json())
        .then(data => {

            if (!data) return;

            const canvas = document.getElementById("ticketsStatusChart");
            if (!canvas) return;

            const companies = Object.keys(data);

            const estados = ["abierto", "en_curso", "cerrado", "en_espera"];

            const colors = {
                abierto: "#0d6efd",
                en_curso: "#ffc107",
                cerrado: "#198754",
                en_espera: "#6c757d"
            };

            const datasets = estados.map(estado => ({
                label: estado.replace("_", " ").toUpperCase(),
                data: companies.map(c => data[c]?.status_counts?.[estado] ?? 0),
                backgroundColor: colors[estado]
            }));

            if (canvas.chartInstance) {
                canvas.chartInstance.destroy();
            }

            canvas.chartInstance = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: companies,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    }
                }
            });

        }).catch(err => console.error("❌ Error tickets-status-stats:", err));
    }

    /* =========================================
       ADMIN
    ==========================================*/
    if (role === "admin") {

        /* USUARIOS POR ROL */
        fetchWithToken("/roles-stats")
        .then(res => res.json())
        .then(roleData => {

            if (!roleData) return;

            const canvas = document.getElementById("rolesChart");
            if (!canvas) return;

            const companies = Object.keys(roleData);
            let rolesSet = new Set();

            companies.forEach(company => {
                Object.keys(roleData[company].roles).forEach(r => rolesSet.add(r));
            });

            const roles = [...rolesSet];

            const datasets = roles.map((rol, i) => ({
                label: rol,
                data: companies.map(c => roleData[c].roles[rol] || 0),
                backgroundColor: ['#006D77', '#F4A300', '#9B5DE5', '#0d6efd'][i % 4]
            }));

            if (canvas.chartInstance) {
                canvas.chartInstance.destroy();
            }

            canvas.chartInstance = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: companies,
                    datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

        }).catch(err => console.error("❌ Error roles-stats:", err));


        /* SATISFACCIÓN */
        fetchWithToken("https://fixflow-endpoints.onrender.com/api/satisfaction/satisfaction-stats/")
        .then(res => res.json())
        .then(data => {

            if (!data) return;

            if (!document.getElementById("satisfactionContainer")) {

                const container = document.createElement("div");
                container.className = "bg-white p-8 shadow-lg rounded-2xl border border-gray-100 mt-8 text-center";
                container.id = "satisfactionContainer";

                container.innerHTML = `
                <h3 class="text-2xl font-bold text-[#003F4E] mb-4">
                    Nivel de Satisfacción
                </h3>
                <p class="text-lg mb-2">
                    <strong>Promedio General:</strong> ${data.overall_csat} ⭐
                </p>
                <p class="text-sm text-gray-500 mb-6">
                    Total de reseñas: ${data.total_reviews}
                </p>
                <div class="mx-auto" style="width:100%; max-width:600px;">
                    <canvas id="satisfactionChart"></canvas>
                </div>
                `;

                document.querySelector(".md\\:col-span-2")?.appendChild(container);
            }

            const canvas = document.getElementById("satisfactionChart");
            if (!canvas) return;

            const labels = Object.keys(data.rating_distribution);
            const values = Object.values(data.rating_distribution);

            if (canvas.chartInstance) {
                canvas.chartInstance.destroy();
            }

            canvas.chartInstance = new Chart(canvas, {
                type: "bar",
                data: {
                    labels: labels.map(l => `${l} ⭐`),
                    datasets: [{
                        data: values,
                        backgroundColor: "#006D77"
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

        }).catch(err => console.error("❌ Error satisfacción:", err));
    }

});
</script>

</x-app-layout>
