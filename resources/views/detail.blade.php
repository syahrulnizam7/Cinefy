@extends('layouts.app')

@section('content')
    <div class="relative w-full text-white" x-data="detail()">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('https://image.tmdb.org/t/p/w1280{{ $detail['backdrop_path'] }}');">
            <div class="absolute inset-0 bg-gradient-to-l from-black/80 via-black/50 to-transparent backdrop-blur-sm"></div>
        </div>

        <div class="relative container mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-8 h-full">
            <!-- Poster with Hover Effect and Play Icon -->
            <div class="w-2/5 md:w-1/4 group relative overflow-hidden cursor-pointer" x-data="{ open: false, trailerUrl: null }">
                <img src="https://image.tmdb.org/t/p/w500{{ $detail['poster_path'] }}"
                    alt="{{ $detail['title'] ?? $detail['name'] }}"
                    class="w-full rounded-lg shadow-lg transition-all duration-500 group-hover:blur-sm group-hover:scale-105">
                <div
                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button
                        @click="open = true; trailerUrl = 'https://www.youtube.com/embed/{{ $trailerKey }}?autoplay=1';"
                        class="relative p-3 bg-transparent rounded-full border-4 border-white group hover:bg-transparent flex items-center justify-center">
                        <ion-icon name="play" class="text-white text-3xl"></ion-icon>
                    </button>
                </div>

                <div x-show="open" x-transition:enter="transition opacity-0 ease-in-out duration-500"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition opacity-100 ease-in-out duration-500"
                    x-transition:leave-start="opacity-0" @click.away="open = false"
                    class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-75 backdrop-blur-md">
                    <div x-show="open" x-transition:enter="transition transform ease-out duration-500 scale-95 opacity-0"
                        x-transition:enter-end="scale-100 opacity-100"
                        x-transition:leave="transition transform ease-in duration-500 scale-105 opacity-0"
                        x-transition:leave-start="scale-100 opacity-100"
                        class="relative bg-gray-900 rounded-lg w-full max-w-4xl p-6">
                        <button @click="open = false; trailerUrl = null;"
                            class="absolute top-4 right-6 text-red-500 text-xl font-bold">X</button>
                        <iframe x-bind:src="trailerUrl" frameborder="0" class="w-full h-80 rounded-lg shadow-lg"
                            allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="w-full md:w-3/5 lg:w-1/2">
                <p class="text-sm text-gray-400 uppercase">Season 1 |
                    {{ \Carbon\Carbon::parse($detail['release_date'] ?? $detail['first_air_date'])->year }}</p>
                <h1 class="text-4xl font-bold">{{ $detail['title'] ?? $detail['name'] }}</h1>

                <div class="flex items-center space-x-2 mt-2">
                    <div class="flex space-x-1">
                        @for ($i = 0; $i < floor($detail['vote_average'] / 2); $i++)
                            <span class="text-red-500">&#9733;</span>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-300">{{ number_format($detail['vote_average'], 1) }}/10</p>
                </div>

                <div class="mt-2">
                    @foreach ($detail['genres'] as $genre)
                        <span class="bg-red-600 text-white px-3 py-1 text-sm rounded-full">{{ $genre['name'] }}</span>
                    @endforeach
                </div>

                <p class="mt-4 text-gray-300">{{ Str::limit($detail['overview'], 200, '...') }}</p>

                <div class="mt-6 flex space-x-4">
                    <button @click="addToWatched" class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-600">
                        Watched
                    </button>
                </div>

                <button
                    class="border border-gray-300 px-6 py-3 rounded-full transition-colors duration-300 hover:bg-gray-800">+
                    Save for Later</button>

                <div class="mt-6 grid grid-cols-2 md:grid-cols-2 gap-6">
                    <div>
                        <p class="font-semibold text-lg text-white">{{ $director['name'] ?? 'N/A' }}</p>
                        <p class="text-gray-400">Director</p>
                    </div>
                    <div>
                        <p class="font-semibold text-lg text-white">{{ $writers ? implode(', ', $writers) : 'N/A' }}</p>
                        <p class="text-gray-400">Writers</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Cast Section -->
    <div class="container mx-auto px-6 py-10">
        <h2 class="text-2xl font-semibold text-white mb-4">Cast</h2>
        <div class="flex overflow-x-auto space-x-4 pb-4 scrollbar-hidden">
            @foreach ($cast as $actor)
                <div class="flex-none w-32 text-center group relative">
                    <img src="https://image.tmdb.org/t/p/w500{{ $actor['profile_path'] ?? '/default-profile.jpg' }}"
                        alt="{{ $actor['name'] }}"
                        class="w-full rounded-full shadow-lg mb-2 transition-all duration-500 group-hover:rotate-2 group-hover:brightness-110 group-hover:shadow-2xl cursor-pointer">
                    <p class="font-semibold text-sm text-white">{{ $actor['name'] }}</p>
                    <p class="text-xs text-gray-400">As {{ $actor['character'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("detail", () => ({
                addToWatched() {
                    fetch("{{ route('watched.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                tmdb_id: "{{ $detail['id'] }}",
                                title: "{{ $detail['title'] ?? $detail['name'] }}",
                                poster_path: "{{ $detail['poster_path'] }}",
                                type: "{{ $type }}",
                                vote_average: "{{ $detail['vote_average'] }}", // Pastikan nilai vote_average ada
                                release_date: "{{ $detail['release_date'] ?? $detail['first_air_date'] }}", // Pastikan release_date ada
                            }),

                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.message) {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan, coba lagi.");
                        });
                }


            }));
        });
    </script>
@endsection
