<x-app-layout>
    {{-- 
        Define el encabezado de la página que se mostrará en el layout.
        Usamos el color oscuro de la marca.
    --}}
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#003F4E] leading-tight">
            {{ __('Bienvenido(a) a tu Panel de Control, ') }}{{ Auth::user()->name }}
        </h2>
    </x-slot>

    {{-- Contenido principal del Dashboard --}}
    <div class="py-12 bg-[#F2F2F2] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <h1 class="text-4xl font-extrabold text-[#003F4E] mb-10 border-b-2 border-gray-300 pb-3">
                Resumen de Operaciones
            </h1>

            <!-- GRID DE TARJETAS DE ESTADO -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                
                {{-- Tarjeta 1: Tickets Pendientes (Color Naranja - Prioridad) --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-xl border-l-8 border-[#F4A300] transform hover:scale-[1.02] transition duration-300 cursor-pointer">
                    <div class="p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Tickets Pendientes</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-5xl font-extrabold text-[#F4A300]">42</span>
                            <span class="text-4xl text-[#F4A300]">🛠️</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">Solicitudes que requieren una acción inmediata.</p>
                    </div>
                    <a href="#" class="block bg-gray-100 text-center py-2 text-[#F4A300] font-medium text-sm hover:bg-gray-200 transition duration-150 rounded-b-xl">
                        Ver Lista de Espera →
                    </a>
                </div>

                {{-- Tarjeta 2: Tickets Resueltos (Color Azul Petróleo - Éxito) --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-xl border-l-8 border-[#006D77] transform hover:scale-[1.02] transition duration-300 cursor-pointer">
                    <div class="p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Tickets Resueltos (Mes)</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-5xl font-extrabold text-[#006D77]">185</span>
                            <span class="text-4xl text-[#006D77]">✅</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">Promedio de solución y eficiencia operativa.</p>
                    </div>
                    <a href="#" class="block bg-gray-100 text-center py-2 text-[#006D77] font-medium text-sm hover:bg-gray-200 transition duration-150 rounded-b-xl">
                        Ver Historial Completo →
                    </a>
                </div>

                {{-- Tarjeta 3: Tiempo Promedio de Respuesta (Color Oscuro - Indicador Clave) --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-xl border-l-8 border-[#003F4E] transform hover:scale-[1.02] transition duration-300 cursor-pointer">
                    <div class="p-6">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Tiempo Promedio</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-5xl font-extrabold text-[#003F4E]">3.4h</span>
                            <span class="text-4xl text-[#003F4E]">⏱️</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-3">Tiempo promedio para la primera respuesta al cliente.</p>
                    </div>
                    <a href="#" class="block bg-gray-100 text-center py-2 text-[#003F4E] font-medium text-sm hover:bg-gray-200 transition duration-150 rounded-b-xl">
                        Ver Reporte KPI →
                    </a>
                </div>

            </div>
            
            {{-- Sección de Gráfica General de Tickets --}}
            <div class="bg-white shadow-2xl rounded-xl p-8">
                <h2 class="text-2xl font-bold text-[#003F4E] mb-6 border-b pb-3 border-gray-200">
                    Distribución de Estatus de Tickets Pendientes
                </h2>

                {{-- Contenedor de la Gráfica --}}
                <div class="h-96 w-full flex items-center justify-center">
                    <canvas id="pendingTicketsChart" class="w-full h-full"></canvas>
                </div>
            </div>

        </div>
    </div>
    
</x-app-layout>

{{-- El bloque de scripts usa @push('scripts') para inyectar scripts en el layout principal --}}
@push('scripts')
    <!-- ¡IMPORTANTE! Eliminamos el CDN de Chart.js aquí ya que se cargó en layouts/app.blade.php -->
    <script>
        // Usamos DOMContentLoaded para asegurar que el Canvas esté disponible al momento de la ejecución.
        document.addEventListener('DOMContentLoaded', function () {
            
            const canvas = document.getElementById('pendingTicketsChart');
            
            // Si el elemento no existe (ej. en otra vista), salimos.
            if (!canvas) return; 

            const ctx = canvas.getContext('2d');

            // Colores de la paleta FixFlow para la gráfica
            const FIXFLOW_COLORS = {
                PRIMARY: '#F4A300',  // Naranja
                SECONDARY: '#006D77', // Azul Petróleo
                DARK: '#003F4E',      // Oscuro
                LIGHT_GRAY: '#BBBBBB'
            };

            const data = {
                labels: ['Pendiente', 'Asignado', 'En Espera del Cliente', 'Escalado'],
                datasets: [{
                    label: 'Cantidad de Tickets',
                    data: [25, 10, 5, 2], // Total: 42 tickets pendientes
                    backgroundColor: [
                        FIXFLOW_COLORS.PRIMARY,
                        FIXFLOW_COLORS.SECONDARY,
                        FIXFLOW_COLORS.LIGHT_GRAY,
                        FIXFLOW_COLORS.DARK,
                    ],
                    borderColor: '#FFFFFF', // Borde blanco para separar las secciones
                    borderWidth: 2
                }]
            };

            const config = {
                type: 'doughnut', // Gráfica de dona para mostrar proporciones
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', // Hace que sea una dona más ancha
                    plugins: {
                        legend: {
                            position: 'bottom', // Mueve la leyenda a la parte inferior
                            labels: {
                                color: FIXFLOW_COLORS.DARK,
                                font: {
                                    size: 14,
                                    family: 'sans-serif'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Tickets Pendientes por Estatus',
                            color: FIXFLOW_COLORS.DARK,
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        }
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
@endpush