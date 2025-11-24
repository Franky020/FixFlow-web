<x-app-layout>

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('Comentarios de Satisfacción (Feedback)') }}
        </h2>
    </x-slot>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="py-6">
        <div class="bg-white shadow sm:rounded-lg p-6">

            @if(isset($error))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Error:</strong>
                    <span>{{ $error }}</span>
                </div>
            @endif

            <h3 class="text-2xl font-bold mb-4 text-[#006D77]">
                Listado de Comentarios
            </h3>

            @if(count($comments) > 0)
                <div class="space-y-4">
                    @foreach ($comments as $comment)
                        <div class="border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition bg-gray-50">

                            <div class="flex justify-between items-start mb-3">
                                <p class="text-sm text-gray-500">
                                    <strong>ID:</strong> {{ $comment['id'] }} |
                                    <strong>Ticket:</strong> {{ $comment['ticket'] }}
                                </p>

                                {{-- Rating --}}
                                <div class="flex items-center text-[#F4A300]">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($comment['rating']))
                                            <i data-lucide="star" class="w-5 h-5 fill-current"></i>
                                        @else
                                            <i data-lucide="star" class="w-5 h-5 text-gray-300"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-gray-700 font-semibold">
                                        {{ number_format($comment['rating'], 1) }} / 5
                                    </span>
                                </div>
                            </div>

                            <p class="text-gray-900 text-lg font-medium mb-2">
                                {{ $comment['message'] }}
                            </p>

                            <p class="text-xs text-gray-500 text-right">
                                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                            </p>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500 border border-dashed rounded-lg">
                    <p class="text-lg">No se encontraron comentarios o hubo un problema al cargar los datos.</p>
                    <i data-lucide="frown" class="w-8 h-8 mx-auto mt-4"></i>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
