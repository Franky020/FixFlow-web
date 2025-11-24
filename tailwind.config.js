import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // 🎨 PALETA DE COLORES FIXFLOW
            colors: {
                // Colores Principales
                'ff-primary': '#F4A300',   // 🟧 Yellow-Orange (Naranja: Botones de acción)
                'ff-secondary': '#006D77', // 🟦 Teal (Azul Petróleo: Base, Nav, Encabezados)
                // Colores Secundarios/Utilidad
                'ff-white': '#FFFFFF',     // Blanco
                'ff-dark': '#003F4E',      // Azul Oscuro (Texto principal, títulos)
                'ff-bg-light': '#F2F2F2',  // Gris Claro (Fondo de página y tarjetas)
                
                // Colores de Estado
                'ff-success': '#3CB371',   // Verde Éxito (Activo o Resuelto)
                'ff-error': '#E74C3C',     // Rojo Suave (Inactivo, Cerrado o Error)
            },
            // FIN DE PALETA FIXFLOW
        },
    },

    plugins: [forms],
};