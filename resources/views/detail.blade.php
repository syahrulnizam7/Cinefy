@extends('layouts.app')

@section('content')
    <style>
        @keyframes popIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pop-in {
            animation: popIn 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }
    </style>

    <div class="relative w-full text-white" x-data="detail()">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('https://image.tmdb.org/t/p/w1280{{ $detail['backdrop_path'] }}');">
            <div class="absolute inset-0 bg-gradient-to-l from-black/80 via-black/50 to-transparent backdrop-blur-sm"></div>
        </div>

        <div class="relative  mx-auto lg:px-16 px-6 py-20 lg:py-32 flex flex-col md:flex-row items-center gap-8 h-full"
            x-data="{
                showWatchedModal: false,
                showDeleteModal: false,
                showLoginModal: false,
                rating: 3,
                review: '',
                watched: false,
                isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
                open: false,
                trailerUrl: null,
                showShareModal: false,
            
            }" x-init="checkIfWatched()">
            <!-- Container Poster -->
            <div class="w-2/5 md:w-1/4">
                <div class="group relative overflow-hidden cursor-pointer">
                    <div class="relative w-full rounded-lg overflow-hidden">
                        <img src="{{ $detail['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $detail['poster_path'] : asset('images/noimg.png') }}"
                            alt="{{ $detail['title'] ?? $detail['name'] }}"
                            class="w-full transition-all duration-500 group-hover:blur-sm group-hover:scale-105">

                        <!-- Watch Providers (diletakkan di dalam gambar) -->
                        @if (!empty($watchProviders['flatrate']))
                            <div
                                class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white text-center p-2 z-20">
                                <p class="text-xs font-semibold">Streaming Now:</p>
                                <div class="flex justify-center space-x-2 mt-1">
                                    @foreach ($watchProviders['flatrate'] as $provider)
                                        <a href="{{ $watchProviders['link'] }}" target="_blank" class="group">
                                            <img src="https://image.tmdb.org/t/p/w92{{ $provider['logo_path'] }}"
                                                alt="{{ $provider['provider_name'] }}"
                                                class="h-8 w-8 rounded-md shadow-lg transition-transform transform hover:scale-110">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

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
                        <div x-show="open"
                            x-transition:enter="transition transform ease-out duration-500 scale-95 opacity-0"
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
            </div>


            <!-- Details -->
            <div class="w-full md:w-3/5 lg:w-1/2">
                <p class="text-sm text-gray-400 uppercase">
                    {{ \Carbon\Carbon::parse($detail['release_date'] ?? $detail['first_air_date'])->year }}</p>
                <div class="flex items-center gap-2">
                    <h1 class="text-4xl font-bold">{{ $detail['title'] ?? $detail['name'] }}</h1>

                    <!-- Favorite Button -->
                    <button @click="addToFavorite"
                        :class="favorite ? 'text-yellow-400 hover:text-yellow-500' : 'text-gray-400 hover:text-yellow-400'"
                        class="text-2xl transition-all duration-300 transform hover:scale-110 scale-125">
                        <ion-icon :name="favorite ? 'star' : 'star-outline'"></ion-icon>
                    </button>
                </div>

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

                <p class="mt-4 text-gray-300">{{ \Illuminate\Support\Str::limit($detail['overview'], 200, '...') }}</p>


                <div class="mt-6 flex space-x-4">

                    <!-- Watched Button -->

                    <button
                        @click="isLoggedIn ? (watched ? showDeleteModal = true : showWatchedModal = true) : showLoginModal = true"
                        :class="watched ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                        class="w-full sm:w-auto text-center flex items-center justify-center text-white px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 hover:shadow-lg">

                        <template x-if="watched">
                            <span class="flex items-center">
                                <ion-icon name="checkmark-circle" class="text-white text-lg"></ion-icon>
                                <span class="ml-2">Watched</span>
                            </span>
                        </template>

                        <template x-if="!watched">
                            <span class="flex items-center justify-center">Watched</span>
                        </template>
                    </button>


                    <!-- Modal I Watched -->
                    <template x-teleport="body">
                        <div x-show="showWatchedModal" x-transition:enter="animate-fade-in"
                            x-transition:leave="animate-fade-out"
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50">
                            <div x-show="showWatchedModal" x-transition:enter="animate-pop-in"
                                x-transition:leave="transition ease-in duration-200 opacity-0"
                                class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white p-6 rounded-lg shadow-xl max-w-2xl w-full relative">
                                <!-- Close Button -->
                                <button @click="showWatchedModal = false"
                                    class="absolute top-4 right-4 text-gray-400 hover:text-white">
                                    <ion-icon name="close" class="text-2xl"></ion-icon>
                                </button>

                                <h2 class="text-xl font-bold mb-4">I Watched...</h2>

                                <!-- Movie Info -->
                                <div class="flex items-start space-x-6">
                                    <!-- Poster -->
                                    <img src="{{ $detail['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $detail['poster_path'] : asset('images/noimg.png') }}"
                                        alt="Poster" class="w-32 h-48 rounded-lg shadow-md">

                                    <!-- Movie Details -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold">{{ $detail['title'] ?? $detail['name'] }}</h3>
                                        <p class="text-sm text-gray-400 mt-1">Genre:
                                            {{ implode(', ', array_map(fn($g) => $g['name'], $detail['genres'] ?? [])) }}
                                        </p>
                                        <p class="text-sm text-gray-300 mt-2 line-clamp-3">
                                            {{ $detail['overview'] ?? 'No description available.' }}</p>
                                    </div>
                                </div>

                                <!-- Rating (Stars) -->
                                <div class="mt-6">
                                    <p class="text-sm font-semibold">Your Rating:</p>
                                    <div class="flex space-x-2 mt-1">
                                        <template x-for="star in 5">
                                            <span @click="rating = star" class="cursor-pointer text-yellow-400 text-2xl">
                                                <ion-icon :name="star <= rating ? 'star' : 'star-outline'"></ion-icon>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Review Input -->
                                <div class="mt-4">
                                    <p class="text-sm font-semibold">Your Review:</p>
                                    <textarea x-model="review"
                                        class="w-full bg-gray-800 border border-gray-700 rounded-md p-2 text-sm text-white focus:outline-none focus:ring focus:border-blue-400 mt-2"
                                        rows="3" placeholder="Write your thoughts..."></textarea>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-6 flex justify-end space-x-4">
                                    <button @click="showWatchedModal = false"
                                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-full">Cancel</button>
                                    <button @click="addToWatched"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-full">Save</button>
                                </div>
                            </div>
                        </div>
                    </template>


                    <!-- Modal Konfirmasi Hapus -->
                    <div x-show="showDeleteModal" x-transition:enter="animate-pop-in"
                        x-transition:leave="transition ease-in duration-200 opacity-0"
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 backdrop-blur">
                        <div
                            class="relative bg-gradient-to-b from-gray-800 via-gray-900 to-black rounded-lg shadow-lg w-full max-w-md p-6">
                            <!-- Tombol Tutup -->
                            <button @click="showDeleteModal = false"
                                class="absolute top-3 right-3 text-gray-300 hover:text-white">
                                <ion-icon name="close" class="text-2xl"></ion-icon>
                            </button>

                            <!-- Konten Modal -->
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-white">Konfirmasi Hapus</h2>
                                <p class="mt-4 text-gray-300">Apakah Anda yakin ingin menghapus item ini dari daftar
                                    watched?</p>

                                <!-- Tombol Aksi -->
                                <div class="mt-6 flex justify-center space-x-4">
                                    <button @click="deleteFromWatched"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Yes
                                    </button>
                                    <button @click="showDeleteModal = false"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Watchlist Button -->
                    <button @click="addToWatchlist"
                        :class="{
                            'bg-yellow-600 hover:bg-yellow-700': watchlist,
                            'border-gray-300 hover:bg-gray-800 hover:text-white': !watchlist,
                            'opacity-50 cursor-not-allowed': isLoading
                        }"
                        class="w-full sm:w-auto border px-4 sm:px-6 py-2 sm:py-3 rounded-full transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        :disabled="isLoading">

                        <!-- Loading Spinner -->
                        <template x-if="isLoading">
                            <span class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </template>

                        <!-- Saved State (Bookmark Icon) -->
                        <template x-if="!isLoading && watchlist">
                            <span class="flex items-center">
                                <ion-icon name="bookmark" class="text-white text-lg"></ion-icon>
                                <span class="ml-2">Saved</span>
                            </span>
                        </template>

                        <!-- Default State (+ Save for Later) -->
                        <template x-if="!isLoading && !watchlist">
                            <span class="flex items-center justify-center">+ Save for Later</span>
                        </template>
                    </button>



                    <!-- Share Button -->
                    <button @click="isLoggedIn ? showShareModal = true : showLoginModal = true"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 flex items-center justify-center text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                        <ion-icon name="share" class="text-white text-lg mr-2"></ion-icon>
                        <span>Share</span>
                    </button>

                    <!-- Modal Bagikan -->
                    <template x-teleport="body">
                        <div x-show="showShareModal" x-transition:enter="animate-fade-in"
                            x-transition:leave="animate-fade-out"
                            class="fixed inset-0 w-screen h-screen z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-md">
                            <div x-show="showShareModal" x-transition:enter="animate-pop-in"
                                x-transition:leave="transition ease-in duration-200 opacity-0"
                                class="relative bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6">
                                <!-- Tombol Tutup -->
                                <button @click="showShareModal = false"
                                    class="absolute top-3 right-3 text-gray-300 hover:text-white">
                                    <ion-icon name="close" class="text-2xl"></ion-icon>
                                </button>

                                <!-- Formulir Bagikan -->
                                <h2 class="text-xl font-semibold text-white text-center">Share to Post</h2>
                                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
                                    class="mt-4">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    <input type="hidden" name="images[]"
                                        value="{{ json_encode([
                                            'image' => $detail['poster_path']
                                                ? 'https://image.tmdb.org/t/p/w500' . $detail['poster_path']
                                                : asset('images/noimg.png'),
                                            'type' => $type,
                                            'id' => $detail['id'],
                                            'title' => $detail['title'] ?? $detail['name'],
                                            'vote_average' => $detail['vote_average'],
                                            'release_date' => $detail['release_date'] ?? $detail['first_air_date'],
                                            'genres' => array_map(fn($genre) => $genre['name'], $detail['genres']),
                                        ]) }}">

                                    <!-- Preview Poster & Detail -->
                                    <div class="flex items-start gap-4 mt-4">
                                        <img src="{{ $detail['poster_path']
                                            ? 'https://image.tmdb.org/t/p/w500' . $detail['poster_path']
                                            : asset('images/noimg.png') }}"
                                            alt="{{ $detail['title'] ?? $detail['name'] }}"
                                            class="w-24 h-36 rounded-lg shadow-lg">

                                        <div class="text-white flex-1">
                                            <h3 class="text-lg font-semibold">{{ $detail['title'] ?? $detail['name'] }}
                                            </h3>
                                            <p class="text-sm text-gray-300">⭐
                                                {{ number_format($detail['vote_average'], 1) }}/10</p>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach ($detail['genres'] as $genre)
                                                    <span class="bg-red-600 text-white px-2 py-1 text-xs rounded-full">
                                                        {{ $genre['name'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            <p class="mt-2 text-sm text-gray-400 line-clamp-2">
                                                {{ mb_strimwidth($detail['overview'], 0, 100, '...') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Input Caption -->
                                    <div class="mt-4">
                                        <label class="text-gray-400">Caption:</label>
                                        <textarea name="content" rows="3"
                                            class="w-full bg-gray-800 text-white p-2 rounded-lg border border-gray-600 
                               focus:border-blue-500 focus:ring focus:ring-blue-500/30"
                                            placeholder="Add your caption..." required></textarea>
                                    </div>

                                    <!-- Tombol Kirim -->
                                    <div class="mt-4 flex justify-end">
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition-all duration-300">
                                            Share
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>

                    <!-- Modal Login -->
                    <div x-show="showLoginModal" x-transition:enter="animate-pop-in"
                        x-transition:leave="transition ease-in duration-200 opacity-0"
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 backdrop-blur">
                        <div class="relative bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6">
                            <!-- Tombol Tutup -->
                            <button @click="showLoginModal = false"
                                class="absolute top-3 right-3 text-gray-300 hover:text-white">
                                <ion-icon name="close" class="text-2xl"></ion-icon>
                            </button>

                            <!-- Konten Modal -->
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-white">Please Login</h2>
                                <p class="mt-4 text-gray-300">You must log in first to use this feature.</p>

                                <!-- Tombol Aksi -->
                                <div class="mt-6 flex justify-center space-x-4">
                                    <a href="{{ route('login') }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Login
                                    </a>
                                    <button @click="showLoginModal = false"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Later
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi Hapus -->
                <template x-teleport="body">
                    <div x-show="showDeleteModal" x-transition:enter="animate-fade-in"
                        x-transition:leave="animate-fade-out"
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 backdrop-blur">
                        <div x-show="showDeleteModal" x-transition:enter="animate-pop-in"
                            x-transition:leave="transition ease-in duration-200 opacity-0"
                            class="relative bg-gradient-to-b from-gray-800 via-gray-900 to-black rounded-lg shadow-lg w-full max-w-md p-6">
                            <!-- Tombol Tutup -->
                            <button @click="showDeleteModal = false"
                                class="absolute top-3 right-3 text-gray-300 hover:text-white">
                                <ion-icon name="close" class="text-2xl"></ion-icon>
                            </button>

                            <!-- Konten Modal -->
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-white">Konfirmasi Hapus</h2>
                                <p class="mt-4 text-gray-300">Apakah Anda yakin ingin menghapus item ini dari daftar
                                    watched?
                                </p>

                                <!-- Tombol Aksi -->
                                <div class="mt-6 flex justify-center space-x-4">
                                    <button @click="deleteFromWatched"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Yes
                                    </button>
                                    <button @click="showDeleteModal = false"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>


                <!-- Modal Notifikasi Silakan Login -->
                <template x-teleport="body">
                    <div x-show="showLoginModal" x-transition
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 backdrop-blur">
                        <div class="relative bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6">
                            <!-- Tombol Tutup -->
                            <button @click="showLoginModal = false"
                                class="absolute top-3 right-3 text-gray-300 hover:text-white">
                                <ion-icon name="close" class="text-2xl"></ion-icon>
                            </button>

                            <!-- Konten Modal -->
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-white">Please Login</h2>
                                <p class="mt-4 text-gray-300">You must log in first to use this feature.</p>

                                <!-- Tombol Aksi -->
                                <div class="mt-6 flex justify-center space-x-4">
                                    <a href="{{ route('login') }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Login
                                    </a>
                                    <button @click="showLoginModal = false"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-full transition-all duration-300 shadow">
                                        Later
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Notifikasi -->
                <div x-show="showNotification"
                    x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-cloak>
                    <span>🎉 Successfully added to the Watched list!</span>
                </div>

                <!-- Notifikasi -->
                <div x-show="showNotification2"
                    x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed top-5 right-5 bg-pink-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-cloak>
                    <span>🎉 Successfully delete item from Watched list!</span>
                </div>
                <!-- Notifikasi -->
                <div x-show="showNotificationFavorite"
                    x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-cloak>
                    <span>🎉 Successfully add item to the fav list!</span>
                </div>

                <!-- Notifikasi -->
                <div x-show="showNotificationFavorite2"
                    x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed top-5 right-5 bg-pink-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" x-cloak>
                    <span>🎉 Successfully delete item from Fav list!</span>
                </div>


                <div class="mt-6 grid grid-cols-2 md:grid-cols-2 gap-6">
                    <div>
                        <p class="font-semibold text-lg text-white">{{ $director['name'] ?? 'N/A' }}</p>
                        <p class="text-gray-400">Director</p>
                    </div>
                    <div>
                        <p class="font-semibold text-lg text-white">
                            {{ $writers ? implode(', ', $writers) : 'N/A' }}</p>
                        <p class="text-gray-400">Writers</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Cast Section -->
    <div class=" mx-auto px-6 py-10">
        <h2 class="text-2xl font-semibold text-white mb-4">Cast</h2>
        <div class="flex overflow-x-auto space-x-4 pb-4 scrollbar-hidden">
            @foreach ($cast as $actor)
                <div class="flex-none w-32 text-center group relative cast-item">
                    <a href="{{ route('cast.show', ['id' => $actor['id']]) }}">
                        <img src="{{ $actor['profile_path'] ? 'https://image.tmdb.org/t/p/w500' . $actor['profile_path'] : 'https://res.cloudinary.com/dj2pofe14/image/upload/images/noimg.png' }}"
                            alt="{{ $actor['name'] }}"
                            class="w-full rounded-full shadow-lg mb-2 transition-all duration-500 group-hover:rotate-2 group-hover:brightness-110 group-hover:shadow-2xl cursor-pointer">
                    </a>
                    <p class="font-semibold text-sm text-white">{{ $actor['name'] }}</p>
                    <p class="text-xs text-gray-400">As {{ $actor['character'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        /* Elemen cast-item disembunyikan pada awalnya dengan transformasi flip */
        .cast-item {
            opacity: 0;
            transform: rotateY(90deg);
            /* Posisi terbalik pada sumbu Y */
            animation: flipIn 2s ease-out forwards;
            /* Ubah durasi menjadi 1 detik */
        }

        /* Animasi flip masuk */
        @keyframes flipIn {
            0% {
                opacity: 0;
                transform: rotateY(90deg);
                /* Posisi terbalik */
            }

            100% {
                opacity: 1;
                transform: rotateY(0deg);
                /* Posisi normal */
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const castItems = document.querySelectorAll('.cast-item');

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Menambahkan kelas 'visible' untuk memulai animasi
                        entry.target.classList.add('visible');
                        observer.unobserve(entry
                            .target); // Hentikan pengamatan setelah elemen terlihat
                    }
                });
            }, {
                threshold: 0.2 // 20% dari elemen terlihat sebelum animasi dimulai
            });

            castItems.forEach((item, index) => {
                // Menghitung delay berdasarkan urutan item, memperpanjang delay menjadi 0.2 detik per item
                const delay = (index + 1) * 0.3; // Menambahkan delay lebih besar (0.2 detik per item)
                item.style.animationDelay = `${delay}s`; // Set delay dinamis
                observer.observe(item);
            });
        });
    </script>
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("detail", () => ({
                watched: false,
                showWatchedModal: false,
                showNotification: false,
                showNotification2: false,
                showDeleteModal: false,

                watchlist: false,
                showNotificationWatchlist: false,
                showNotificationRemoveWatchlist: false,

                isLoading: false,
                favorite: false,
                showNotificationFavorite: false,
                showNotificationFavorite2: false,
                showDeleteModalFavorite: false,

                init() {
                    this.checkIfWatched();
                    this.checkIfWatchlist();
                    this.checkIfFavorite();
                },

                checkIfWatched() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('watched.index') }}?tmdb_id=" + tmdb_id, {
                            method: "GET",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.watched) {
                                this.watched = true;
                            }
                        })
                        .catch(error => console.error("Error checking watched status:", error));
                },
                checkIfFavorite() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('favorite.index') }}?tmdb_id=" + tmdb_id, {
                            method: "GET",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.favorite) {
                                this.favorite = true;
                            }
                        })
                        .catch(error => console.error("Error checking favorite status:", error));
                },


                checkIfWatchlist() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('watchlist.index') }}?tmdb_id=" + tmdb_id, {
                            method: "GET",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.watchlist) {
                                this.watchlist = true;
                            }
                        })
                        .catch(error => console.error("Error checking watchlist status:", error));
                },

                removeFromWatchlist() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('watchlist.destroy') }}", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                tmdb_id
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.watchlist = false;
                                this.showNotificationRemoveWatchlist = true;

                                setTimeout(() => {
                                    this.showNotificationRemoveWatchlist = false;
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan, coba lagi.");
                        });
                },


                addToWatched() {
                    let tmdb_id = "{{ $detail['id'] }}";
                    let title = "{{ $detail['title'] ?? $detail['name'] }}";
                    let poster_path = "{{ $detail['poster_path'] }}";
                    let type = "{{ $type }}";
                    let vote_average = "{{ $detail['vote_average'] ?? 0 }}";
                    let release_date =
                        "{{ $detail['release_date'] ?? ($detail['first_air_date'] ?? '') }}";
                    let rating = this.rating;
                    let review = this.review;

                    if (!tmdb_id) {
                        alert("Data tidak lengkap, tidak bisa menyimpan.");
                        return;
                    } else {
                        fetch("{{ route('watched.store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    tmdb_id,
                                    title,
                                    poster_path,
                                    type,
                                    vote_average,
                                    release_date,
                                    rating,
                                    review,
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    this.watched = true;
                                    this.showNotification = true;
                                    this.showWatchedModal = false; // Tutup modal


                                    // Sembunyikan notifikasi setelah 3 detik
                                    setTimeout(() => {
                                        this.showNotification = false;
                                    }, 3000);
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("Terjadi kesalahan, coba lagi.");
                            });

                    }
                },

                addToFavorite() {
                    let tmdb_id = "{{ $detail['id'] }}";
                    let title = "{{ $detail['title'] ?? $detail['name'] }}";
                    let poster_path = "{{ $detail['poster_path'] }}";
                    let type = "{{ $type }}";
                    let vote_average = "{{ $detail['vote_average'] ?? 0 }}";
                    let release_date =
                        "{{ $detail['release_date'] ?? ($detail['first_air_date'] ?? '') }}";

                    if (!tmdb_id) {
                        alert("Data tidak lengkap, tidak bisa menyimpan.");
                        return;
                    }

                    if (!"{{ Auth::check() }}") { // Cek apakah pengguna login
                        this.showLoginModal = true; // Tampilkan modal login
                        return;
                    }
                    if (this.favorite) {
                        this.showDeleteModalFavorite = true; // Tampilkan modal konfirmasi
                    } else {
                        fetch("{{ route('favorite.store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    tmdb_id,
                                    title,
                                    poster_path,
                                    type,
                                    vote_average,
                                    release_date,
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    this.favorite = true;
                                    this.showNotificationFavorite = true;

                                    // Sembunyikan notifikasi setelah 3 detik
                                    setTimeout(() => {
                                        this.showNotificationFavorite = false;
                                    }, 3000);
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("Terjadi kesalahan, coba lagi.");
                            });
                    }
                },

                addToWatchlist() {
                    let tmdb_id = "{{ $detail['id'] }}";
                    let title = "{{ $detail['title'] ?? $detail['name'] }}";
                    let poster_path = "{{ $detail['poster_path'] }}";
                    let type = "{{ $type }}";
                    let vote_average = "{{ $detail['vote_average'] ?? 0 }}";
                    let release_date =
                        "{{ $detail['release_date'] ?? ($detail['first_air_date'] ?? '') }}";

                    if (!tmdb_id) {
                        alert("Data tidak lengkap, tidak bisa menyimpan.");
                        return;
                    }
                    if (!"{{ Auth::check() }}") { // Cek apakah pengguna login
                        this.showLoginModal = true; // Tampilkan modal login
                        return;
                    }

                    this.isLoading = true; // Aktifkan loading spinner

                    if (this.watchlist) {
                        // Jika sudah di watchlist, hapus dari watchlist
                        fetch("{{ route('watchlist.destroy') }}", {
                                method: "DELETE",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    tmdb_id
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.watchlist = false; // Update state watchlist
                                    this.showNotificationRemoveWatchlist = true;

                                    setTimeout(() => {
                                        this.showNotificationRemoveWatchlist = false;
                                    }, 3000);
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("Terjadi kesalahan, coba lagi.");
                            })
                            .finally(() => {
                                this.isLoading = false; // Matikan loading spinner
                            });
                    } else {
                        // Jika belum di watchlist, tambahkan ke watchlist
                        fetch("{{ route('watchlist.store') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    tmdb_id,
                                    title,
                                    poster_path,
                                    type,
                                    vote_average,
                                    release_date,
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    this.watchlist = true; // Update state watchlist
                                    this.showNotificationWatchlist = true;

                                    setTimeout(() => {
                                        this.showNotificationWatchlist = false;
                                    }, 3000);
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                alert("Terjadi kesalahan, coba lagi.");
                            })
                            .finally(() => {
                                this.isLoading = false; // Matikan loading spinner
                            });
                    }
                },

                deleteFromWatched() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('watched.destroy') }}", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                tmdb_id
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.watched = false;
                                this.showDeleteModal = false; // Tutup modal
                                this.showNotification2 = true;

                                // Sembunyikan notifikasi setelah 3 detik
                                setTimeout(() => {
                                    this.showNotification2 = false;
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan, coba lagi.");
                        });
                },
                deleteFromFavorite() {
                    let tmdb_id = "{{ $detail['id'] }}";

                    fetch("{{ route('favorite.destroy') }}", {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            },
                            body: JSON.stringify({
                                tmdb_id
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.favorite = false;
                                this.showDeleteModalFavorite = false; // Tutup modal
                                this.showNotificationFavorite2 = true;

                                // Sembunyikan notifikasi setelah 3 detik
                                setTimeout(() => {
                                    this.showNotificationFavorite2 = false;
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan, coba lagi.");
                        });
                },
            }));
        });
    </script>
@endsection
