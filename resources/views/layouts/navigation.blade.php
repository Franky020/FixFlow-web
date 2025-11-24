<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navegación con Slider Activo</title>
    <!-- Incluye Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Se asegura que el slider esté inicialmente invisible y que la transición sea suave */
        #nav-slider {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
            opacity: 0; /* Oculto inicialmente */
        }
        /* Clase para simular el enlace activo */
        .nav-link.active {
            color: #4f46e5; /* indigo-600 */
        }
    </style>
</head>
<body class="bg-gray-50 font-[Inter]">

    <!-- Navegación simulando el Blade y Alpine.js -->
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <!-- Logo/Inicio -->
                        <a href="#" class="font-bold text-xl text-gray-800">
                            App Logo
                        </a>
                    </div>

                    <!-- Enlaces de navegación de escritorio -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex relative" id="navbar-container">

                        <!-- La barra deslizante (Slider) -->
                        <span id="nav-slider" class="absolute bottom-0 h-0.5 bg-indigo-600"></span>

                        <ul id="navbar-links" class="flex space-x-8">
                            <li>
                                <a href="#" data-route="dashboard" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <!-- Enlace activo por defecto -->
                                <a href="#" data-route="users.index" class="nav-link active inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-indigo-700 focus:outline-none focus:text-indigo-800 focus:border-indigo-700 transition duration-150 ease-in-out">
                                    Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="#" data-route="tickets.index" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Tickets
                                </a>
                            </li>
                            <li>
                                <a href="#" data-route="spare_parts.index" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Refacciones
                                </a>
                            </li>
                            <li>
                                <a href="#" data-route="reports.index" class="nav-link inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Reportes
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenedor para el mensaje de estado de la demo -->
    <div id="status-message" class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8 p-3 text-sm bg-indigo-100 text-indigo-800 rounded-lg">
        El enlace "Usuarios" es el activo por defecto. Pasa el ratón (hover) sobre otros enlaces para ver el efecto deslizante.
    </div>

    <!-- Script para la lógica del slider -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slider = document.getElementById('nav-slider');
            const linksContainer = document.getElementById('navbar-links');
            const navLinks = linksContainer ? linksContainer.querySelectorAll('.nav-link') : [];
            let activeLink = null;

            // 1. Encuentra el enlace activo (el que tiene la clase 'active')
            activeLink = Array.from(navLinks).find(link => link.classList.contains('active'));
            
            // 2. Función para posicionar el slider
            const positionSlider = (targetLink) => {
                if (!slider || !linksContainer || !targetLink) return;

                // Obtenemos las posiciones relativas
                const containerRect = linksContainer.getBoundingClientRect();
                const linkRect = targetLink.getBoundingClientRect();

                // Calculamos el desplazamiento a la izquierda dentro del contenedor
                const offsetLeft = linkRect.left - containerRect.left;

                // Aplicamos el ancho y la posición
                slider.style.width = `${linkRect.width}px`;
                slider.style.transform = `translateX(${offsetLeft}px)`;
                
                // Hacemos visible el slider
                slider.classList.remove('opacity-0');
                slider.classList.add('opacity-100');
            };

            // Inicializa la posición al cargar la página
            if (activeLink) {
                positionSlider(activeLink);
            }

            // 3. Añadir escuchadores de eventos
            navLinks.forEach(link => {
                // Efecto al pasar el ratón (Hover)
                link.addEventListener('mouseenter', (e) => {
                    positionSlider(e.currentTarget);
                });

                // Efecto al salir del enlace: vuelve al enlace activo original
                link.addEventListener('mouseleave', () => {
                    if (activeLink) {
                        positionSlider(activeLink);
                    } else {
                         // Si no hay enlace activo, esconde el slider
                        slider.style.width = '0px';
                        slider.style.transform = `translateX(0px)`;
                        slider.classList.remove('opacity-100');
                        slider.classList.add('opacity-0');
                    }
                });

                // Simulación de clic para cambiar el enlace activo
                link.addEventListener('click', (e) => {
                    // Quitamos la clase 'active' y el estilo de todos los enlaces
                    navLinks.forEach(l => {
                        l.classList.remove('active', 'text-indigo-700', 'border-indigo-400');
                        l.classList.add('text-gray-500', 'border-transparent');
                    });
                    
                    // Marcamos el nuevo enlace como activo
                    e.currentTarget.classList.add('active', 'text-indigo-700', 'border-indigo-400');
                    e.currentTarget.classList.remove('text-gray-500', 'border-transparent');
                    
                    activeLink = e.currentTarget;
                    positionSlider(activeLink);

                    // Actualizar el mensaje de estado
                    document.getElementById('status-message').innerHTML = `El enlace **"${e.currentTarget.innerText}"** es el nuevo enlace activo.`;

                    e.preventDefault(); // Previene la navegación real en la demo
                });
            });
        });
    </script>
</body>
</html>