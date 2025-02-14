@extends('layouts.app')

<!-- Lingkaran Blur dengan Glow gerak -->
<div
    class="fixed top-40 -left-52 md:top-52 lg:top-80 lg:-left-40 w-72 h-72 md:w-[400px] md:h-[400px] bg-green-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-green-500/50 -z-10 animate-moveCircle1">
</div>
<div
    class="fixed -top-32 -right-40 md:-top-44 md:-right-56 w-72 h-72 md:w-[420px] md:h-[420px] bg-pink-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-pink-500/50 -z-10 animate-moveCircle2">
</div>


@section('content')
    <!-- Tambahkan Typed.js di Head atau sebelum penutup </body> -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

    <!-- Hero Section -->
    <section class="relative text-white lg:pt-20 ">
        <div class="relative flex flex-col lg:grid lg:grid-cols-2 gap-8 md:gap-12 items-center w-full md:px-6 ">
            <!-- Bagian Kiri (Text) - Muncul hanya di Desktop -->
            <div class="hidden lg:block z-20">
                <h1 class="hero-title text-3xl md:text-5xl font-bold leading-tight">
                    Track Your Favorite <br><span class="text-blue-500" id="typing-text"></span></br>
                </h1>
                <p class="hero-text text-lg mt-4 md:mt-6 opacity-80">
                    Manage your watched content, discover new shows, and add to your collection seamlessly.
                </p>
                <div class="hero-buttons mt-6 flex gap-3">
                    <a href="#"
                        class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold shadow-lg hover:bg-blue-700 transition">Start
                        Watching</a>
                    <a href="{{ route('explore.index') }}"
                        class="border border-blue-600 text-blue-600 px-5 py-2.5 rounded-full font-semibold hover:bg-blue-600 hover:text-white transition">Explore
                        Now</a>
                </div>
            </div>

            <!-- Bagian Kanan (Gambar) - Muncul dulu di Mobile -->
            <div class="w-full h-full relative flex justify-center">
                <img src="{{ asset('images/hero.png') }}" alt="Hero Image"
                    class="hero-image w-full max-w-md sm:max-w-lg md:max-w-xl h-auto object-contain opacity-70 z-0">

                <!-- Overlay yang menutupi seluruh layar -->
                <div
                    class="absolute h-full top-0 left-0 w-full  flex flex-col justify-center items-center text-center p-4 bg-gradient-to-b from-black/80 via-black/70 to-transparent lg:hidden">
                    <h1 class="text-2xl sm:text-3xl font-bold mt-20">
                        Track Your Favorite <span class="text-blue-500" id="typing-text-mobile"></span>
                    </h1>
                    <p class="text-sm sm:text-base mt-2 sm:mt-4 opacity-80">Manage your watched content, discover new shows,
                        and add to your collection seamlessly.</p>
                    <div class="mt-4 sm:mt-6 flex gap-3 justify-center">
                        <a href="#"
                            class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold shadow-lg hover:bg-blue-700 transition">Start
                            Watching</a>
                        <a href="{{ route('explore.index') }}"
                            class="border border-blue-600 text-blue-600 px-5 py-2.5 rounded-full font-semibold hover:bg-blue-600 hover:text-white transition">Explore
                            Now</a>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <section class="w-full px-4 py-6 md:px-6 text-white relative z-20" x-data="{ trendingType: 'day' }">
        <!-- Header: Trending + Tabs -->
        <div class="flex items-center  mb-6">
            <h2 class="text-xl sm:text-2xl lg:text-3xl  font-bold ">Trending</h2>
            <div class="relative inline-flex ml-4 bg-transparent border border-gray-300 rounded-full p-1 overflow-hidden">
                <!-- Active Indicator -->
                <div class="absolute top-0 bottom-0 bg-blue-900 rounded-full transition-all duration-300"
                    x-bind:style="trendingType === 'day' ? 'left: 0; width: 45%;' : 'left: 40%; width: 60%;'"></div>

                <button @click="trendingType = 'day'"
                    class="relative px-4 py-2 text-sm font-semibold rounded-full transition z-10"
                    :class="trendingType === 'day' ? 'text-green-400' : 'text-white'">
                    Today
                </button>
                <button @click="trendingType = 'week'"
                    class="relative px-4 py-2 text-sm font-semibold rounded-full transition z-10"
                    :class="trendingType === 'week' ? 'text-green-400' : 'text-white'">
                    This Week
                </button>
            </div>
        </div>

        <!-- Trending List -->
        <div class="relative overflow-x-auto overflow-y-hidden scrollbar-hidden pb-6" ">
                <div class="flex gap-4 min-w-max lg:trending-list">
                    <!-- Trending Today -->
                    <template x-if="trendingType === 'day'">
                        <template x-for="item in {{ json_encode($trendingDay) }}" :key="item.id">
                            <a :href="'/detail/' + item.media_type + '/' + item.id"
                                class="relative bg-gray-900 rounded-xl shadow-2xl w-36 lg:w-48 flex-shrink-0 overflow-hidden group lg:trending-item">
                                <div class="relative w-full h-52 lg:h-72 overflow-hidden rounded-xl">
                                    <img :src="'https://image.tmdb.org/t/p/w500' + item.poster_path"
                                        :alt="item.title ?? item.name"
                                        class="w-full h-full object-cover rounded-xl transform transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent">
                                    </div>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <h3 class="text-lg font-extrabold" x-text="item.title ?? item.name"></h3>
                                    <p class="text-sm opacity-80">
                                        <span
                                            x-text="item.release_date ? new Date(item.release_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'">
                                        </span>
                                    </p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span
                                            class="text-sm font-semibold bg-yellow-500 px-3 py-1 rounded-full text-black shadow-md">
                                            ⭐ <span x-text="item.vote_average.toFixed(1)"></span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </template>

                    <!-- Trending This Week -->
                    <template x-if="trendingType === 'week'">
                        <template x-for="item in {{ json_encode($trendingWeek) }}" :key="item.id">
                            <a :href="'/detail/' + item.media_type + '/' + item.id"
                                class="relative bg-gray-900 rounded-xl shadow-2xl w-36 lg:w-48 flex-shrink-0 overflow-hidden group lg:trending-item">
                                <div class="relative w-full h-52 lg:h-72 overflow-hidden rounded-xl">
                                    <img :src="'https://image.tmdb.org/t/p/w500' + item.poster_path"
                                        :alt="item.title ?? item.name"
                                        class="w-full h-full object-cover rounded-xl transform transition-transform duration-300 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent">
                                    </div>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <h3 class="text-lg font-extrabold" x-text="item.title ?? item.name"></h3>
                                    <p class="text-sm opacity-80">
                                        <span
                                            x-text="item.release_date ? new Date(item.release_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'">
                                        </span>
                                    </p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span
                                            class="text-sm font-semibold bg-yellow-500 px-3 py-1 rounded-full text-black shadow-md">
                                            ⭐ <span x-text="item.vote_average.toFixed(1)"></span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </template>
                </div>
            </div>
        </section>

        {{-- Latest Trailers Section --}}
        <section
        class="w-full mt-4 relative mx-auto px-6 py-10 text-white group transition-all duration-700 sm:w-full md:w-full lg:w-full"
        x-data="{ playing: false, trailerUrl: '', stopVideo() { this.trailerUrl = '';
                this.playing = false; } }"
        :style="'background-image: url(' + trailerUrl +
            '); background-size: cover; background-position: center; background-blend-mode: overlay;'">


            <!-- Overlay warna biru semi-transparan dengan vignette & transisi -->
            <div
                class="absolute inset-0 bg-blue-500 bg-opacity-80 transition-all duration-700 group-hover:bg-opacity-60 
bg-[radial-gradient(circle,rgba(0,0,0,0.4)_10%,rgba(0,0,0,0.8)_90%)] group-hover:opacity-50 w-full sm:w-full w- md:w-full lg:w-full">
            </div>

            <!-- Konten -->
            <div class="relative z-10 transition-all duration-700">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-6">Latest Trailers</h2>
                <div class="flex space-x-4 overflow-x-auto scrollbar-hidden">
                     @foreach ($latestTrailers as $movie)
            @if ($movie['trailer_key'])
                <div class="w-60 flex-shrink-0 transition-all duration-700"
                    @mouseover="trailerUrl = 'https://img.youtube.com/vi/{{ $movie['trailer_key'] }}/maxresdefault.jpg'"
                    @click="playing = true; trailerUrl = 'https://www.youtube.com/embed/{{ $movie['trailer_key'] }}?autoplay=1&rel=0&showinfo=0'">

                    <!-- Wrapper untuk Thumbnail & Video -->
                    <div class="relative pb-[56.25%] overflow-hidden rounded-lg shadow-lg group">

                        <!-- Thumbnail dengan ikon play -->
                        <div class="absolute top-0 left-0 w-full h-full bg-black cursor-pointer">
                            <img class="w-full h-full object-cover"
                                src="https://img.youtube.com/vi/{{ $movie['trailer_key'] }}/maxresdefault.jpg"
                                alt="{{ $movie['title'] }}">

                            <!-- Ikon Play di tengah -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="w-14 h-14 bg-white bg-opacity-75 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.5 4.5l9 5-9 5V4.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-sm font-semibold mt-2">{{ $movie['title'] }}</h3>
                    <p class="text-xs opacity-75">
                        {{ \Carbon\Carbon::parse($movie['release_date'])->translatedFormat('d M Y') }}
                    </p>
                </div>
            @endif
            @endforeach
        </div>
        </div>

        <!-- Modal untuk menampilkan video -->
        <div x-show="playing" x-transition:enter="transition opacity-0 ease-in-out duration-500"
            x-transition:enter-end="opacity-100" x-transition:leave="transition opacity-100 ease-in-out duration-500"
            x-transition:leave-start="opacity-0" @click.away="stopVideo()"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-75 backdrop-blur-md">

            <!-- Konten Modal dengan animasi pop-up -->
            <div x-show="playing" x-transition:enter="transition transform ease-out duration-500 scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition transform ease-in duration-500 scale-105 opacity-0"
                x-transition:leave-start="scale-100 opacity-100"
                class="relative bg-white rounded-lg w-full max-w-3xl p-4">

                <!-- Close Button -->
                <button @click="stopVideo()" class="absolute top-4 right-6 text-red-500 text-xl font-bold">
                    X
                </button>

                <!-- Iframe video -->
                <iframe class="w-full h-80 rounded-lg shadow-lg" :src="trailerUrl" frameborder="0"
                    allow="autoplay; encrypted-media" allowfullscreen>
                </iframe>
            </div>
        </div>
    </section>

    <section class="w-full px-4 py-6 md:px-6 text-white relative z-20" x-data="{ popularType: 'movies' }">
        <!-- Header: Popular + Tabs -->
        <div class="flex items-center mb-4 sm:mb-6">
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold">Popular</h2>
            <div class="relative inline-flex ml-4 bg-transparent border border-gray-300 rounded-full p-1 overflow-hidden">
                <!-- Active Indicator -->
                <div class="absolute top-0 bottom-0 bg-blue-900 rounded-full transition-all duration-300"
                    x-bind:style="popularType === 'movies' ? 'left: 0; width: 50%;' : 'left: 40%; width: 60%;'"></div>

                <button @click="popularType = 'movies'"
                    class="relative px-4 py-2 text-sm font-semibold rounded-full transition z-10"
                    :class="popularType === 'movies' ? 'text-green-400' : 'text-white'">
                    Movies
                </button>
                <button @click="popularType = 'tv'"
                    class="relative px-4 py-2 text-sm font-semibold rounded-full transition z-10"
                    :class="popularType === 'tv' ? 'text-green-400' : 'text-white'">
                    TV Shows
                </button>
            </div>
        </div>

        <!-- Popular List -->
        <div class="relative overflow-x-auto overflow-y-hidden scrollbar-hidden pb-6">
            <div class="flex  gap-4 min-w-max lg:trending-list">
                <!-- Popular Movies -->
                <template x-if="popularType === 'movies'">
                    <template x-for="item in {{ json_encode($popularMovies) }}" :key="item.id">
                        <a :href="'/detail/movie/' + item.id"
                            class="relative bg-gray-900 rounded-xl shadow-2xl w-36 lg:w-48 flex-shrink-0 overflow-hidden group lg:trending-item">
                            <div class="relative w-full h-52 lg:h-72 overflow-hidden rounded-xl">
                                <img :src="'https://image.tmdb.org/t/p/w500' + item.poster_path" :alt="item.title"
                                    class="w-full h-full object-cover rounded-xl transform transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent">
                                </div>
                            </div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <h3 class="text-lg font-extrabold" x-text="item.title"></h3>
                                <p class="text-sm opacity-80">
                                    <span
                                        x-text="item.release_date ? new Date(item.release_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'"></span>
                                </p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span
                                        class="text-sm font-semibold bg-yellow-500 px-3 py-1 rounded-full text-black shadow-md">
                                        ⭐ <span x-text="item.vote_average.toFixed(1)"></span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </template>

                <!-- Popular TV Shows -->
                <template x-if="popularType === 'tv'">
                    <template x-for="item in {{ json_encode($popularTV) }}" :key="item.id">
                        <a :href="'/detail/tv/' + item.id"
                            class="relative bg-gray-900 rounded-xl shadow-2xl w-36 lg:w-48 flex-shrink-0 overflow-hidden group">
                            <div class="relative w-full h-52 lg:h-72 overflow-hidden rounded-xl">
                                <img :src="'https://image.tmdb.org/t/p/w500' + item.poster_path" :alt="item.name"
                                    class="w-full h-full object-cover rounded-xl transform transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent">
                                </div>
                            </div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <h3 class="text-lg font-extrabold" x-text="item.name"></h3>
                                <p class="text-sm opacity-80">
                                    <span
                                        x-text="item.first_air_date ? new Date(item.first_air_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'"></span>
                                </p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span
                                        class="text-sm font-semibold bg-yellow-500 px-3 py-1 rounded-full text-black shadow-md">
                                        ⭐ <span x-text="item.vote_average.toFixed(1)"></span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </template>
            </div>
        </div>
    </section>


    <section class="w-full px-4 py-6 md:px-6 text-white relative z-20 overflow-hidden">
        <!-- Gradient Background for Particles -->
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent z-0"></div>

        <!-- Particle Background -->
        <div class="absolute inset-0 z-10 pointer-events-none">
            <template x-for="i in 12" :key="i">
                <span class="absolute text-2xl opacity-50 animate-float"
                    :style="`top: ${10 + Math.random() * 80}%; left: ${10 + Math.random() * 80}%; animation-duration: ${4 + Math.random() * 6}s; animation-delay: ${Math.random() * 3}s;`"
                    x-text="['🎬', '🍿', '🎥'][Math.floor(Math.random() * 3)]">
                </span>
            </template>
        </div>

        <h2
            class="text-2xl sm:text-3xl font-bold mb-6 text-center bg-gradient-to-r from-yellow-600 via-yellow-400 to-yellow-200 animate-pulse bg-clip-text text-transparent relative z-20">
            🎖 Leaderboard
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-20">
            @foreach ($topUsers->chunk(5) as $chunk)
                <div class="space-y-4">
                    @foreach ($chunk as $index => $user)
                        <a href="{{ route('user.detail', ['id' => $user->id]) }}"
                            class="flex items-center bg-white/10 backdrop-blur-lg border border-white/20 p-5 rounded-lg shadow-lg shadow-black/30 transition-all duration-300 hover:scale-105 hover:bg-white/20">
                            <div class="relative">
                                <div
                                    class="w-14 h-14 md:w-16 md:h-16 rounded-full overflow-hidden border-4 border-transparent transition-all duration-300 hover:border-yellow-400">
                                    <img src="{{ $user->profile_photo ?? 'https://res.cloudinary.com/dj2pofe14/image/upload/v1/profile_pictures/default-avatar.png' }}"
                                        alt="{{ $user->name }}"
                                        class="object-cover w-full h-full rounded-full border-2 border-gray-300">

                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-200">{{ $index + 1 }}. {{ $user->name }}
                                </h3>
                                <p class="text-xs ml-2 text-gray-400"><span
                                        class="text-blue-400">@</span>{{ $user->username }}</p>
                                <p class="text-sm text-gray-300">🎬 Watched: <span
                                        class="font-bold text-yellow-400">{{ $user->total_watched }}</span></p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>



    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('particles', () => ({
                init() {
                    setInterval(() => {
                        document.querySelectorAll('.animate-float').forEach(el => {
                            el.style.top = (10 + Math.random() * 80) + '%';
                            el.style.left = (10 + Math.random() * 80) + '%';
                        });
                    }, 6000);
                }
            }));
        });
    </script>

    <style>
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.5;
            }

            25% {
                transform: translateY(-15px) rotate(10deg);
                opacity: 0.7;
            }

            50% {
                transform: translateY(-25px) rotate(-10deg);
                opacity: 1;
            }

            75% {
                transform: translateY(-15px) rotate(10deg);
                opacity: 0.7;
            }

            100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.5;
            }
        }

        .animate-float {
            position: absolute;
            animation: float infinite ease-in-out alternate;
        }
    </style>


    <!-- Inisialisasi Typed.js -->
    <script>
        var options = {
            strings: ["Movies", "Series", "Anime"],
            typeSpeed: 100, // Kecepatan mengetik
            backSpeed: 50, // Kecepatan menghapus
            backDelay: 1500, // Waktu jeda sebelum menghapus
            loop: true, // Agar animasi terus berulang
            showCursor: true, // Menampilkan kursor
            cursorChar: "|", // Simbol kursor
        };

        new Typed("#typing-text", options);
        new Typed("#typing-text-mobile", options);
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            gsap.registerPlugin(ScrollTrigger);

            gsap.from(".trending-item", {
                opacity: 0,
                y: 50,
                stagger: 0.2,
                duration: 1,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: ".trending-list",
                    start: "top 60%",
                    toggleActions: "play none none none",
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Animasi Hero Section
            gsap.from(".hero-title", {
                opacity: 0,
                y: 50,
                duration: 1,
                ease: "power2.out"
            });
            gsap.from(".hero-text", {
                opacity: 0,
                y: 50,
                duration: 1,
                delay: 0.2,
                ease: "power2.out"
            });
            gsap.from(".hero-buttons", {
                opacity: 0,
                y: 50,
                duration: 1,
                delay: 0.4,
                ease: "power2.out",
                stagger: 0.2
            });
            gsap.from(".hero-image", {
                opacity: 0,
                scale: 0.9,
                duration: 1,
                delay: 0.6,
                ease: "power2.out"
            });

            // Animasi Trending List
            gsap.from(".trending-item", {
                opacity: 0,
                y: 30,
                duration: 1,
                ease: "power2.out",
                stagger: 0.2
            });

            // Animasi Latest Trailers
            gsap.from(".latest-trailer", {
                opacity: 0,
                x: -50,
                duration: 1,
                ease: "power2.out",
                stagger: 0.3
            });
        });
    </script>
    <style>
        /* Animasi lingkaran pertama */
        @keyframes moveCircle1 {
            0% {
                transform: translate(0, 0);
            }

            25% {
                transform: translate(250px, -200px);
            }

            /* Adjusted to move closer to center */
            50% {
                transform: translate(-150px, 100px);
            }

            /* Adjusted */
            75% {
                transform: translate(200px, 50px);
            }

            /* Adjusted */
            100% {
                transform: translate(0, 0);
            }
        }

        /* Animasi lingkaran kedua */
        @keyframes moveCircle2 {
            0% {
                transform: translate(0, 0);
            }

            25% {
                transform: translate(-250px, 150px);
            }

            /* Adjusted to move closer to center */
            50% {
                transform: translate(300px, -180px);
            }

            /* Adjusted */
            75% {
                transform: translate(-200px, -50px);
            }

            /* Adjusted */
            100% {
                transform: translate(0, 0);
            }
        }

        .animate-moveCircle1 {
            animation: moveCircle1 8s ease-in-out infinite;
        }

        .animate-moveCircle2 {
            animation: moveCircle2 8s ease-in-out infinite;
        }
    </style>
@endsection
