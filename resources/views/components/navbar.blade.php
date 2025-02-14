<nav x-data="{
    navBottomVisible: true,
    searchOpen: false,
    query: '',
    modalOpen: false,
    results: [],
    performSearch() {
        if (this.query.trim().length < 3) {
            this.results = [];
            return;
        }
        fetch('/index.php/api/search?q=' + encodeURIComponent(this.query))
            .then(response => response.json())
            .then(data => {
                console.log(data.results); // Debugging output
                // Filter results to exclude 'person' type
                this.results = data.results.filter(item => item.media_type !== 'person')
                    .map(item => ({
                        id: item.id,
                        title: item.title || item.name,
                        release_date: item.release_date || item.first_air_date || '',
                        type: item.type,
                        poster_path: item.poster_path.startsWith('http') ? item.poster_path : `https://image.tmdb.org/t/p/w92${item.poster_path}`,
                        average_rating: typeof item.average_rating === 'number' ? item.average_rating.toFixed(1) : 'N/A'
                    }));
            })
            .catch(error => console.error('Error fetching data:', error));
    },

}">

    {{-- Navbar top --}}
    <nav id="navbar"
        class="fixed z-30 top-0 left-0 w-full py-6 px-6 bg-gradient-to-b from-gray-900 to-transparent text-white transition-all duration-300"
        x-data="{
            navOpen: true,
        }">

        <div class="container w-full mx-auto flex justify-between items-center">
            <a href="/"><img src="{{ asset('images/logocinefy.png') }}" alt="My Image"
                    class="h-12 order-1 sm:order-2"></a>
            <!-- Tombol Hamburger di navbar.blade -->
            <button
                @click="navOpen = !navOpen; navBottomVisible = !navBottomVisible; 
$dispatch('toggle-filter-position')"
                id="hamburger"
                class="hover:bg-blue-700 transition bg-blue-600 rounded-md w-12 h-12 flex flex-col items-center justify-center gap-1.5 order-2 sm:order-1 lg:hidden">
                <span class="w-6 h-[2px] bg-white"></span>
                <span class="w-6 h-[2px] bg-white"></span>
                <span class="w-6 h-[2px] bg-white"></span>
            </button>

            <div class="order-3 hidden sm:block">
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-white font-semibold flex items-center gap-2">
                            <!-- Profile Photo -->
                            <img src="{{ auth()->user()->profile_photo ?? asset('images/default-avatar.png') }}"
                                alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-white">

                            <span>{{ auth()->user()->name }}</span>

                        </button>
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-48 bg-gray-800 text-white shadow-lg rounded-xl border border-gray-700 z-10">
                            <ul class="py-2 mx-auto items-center ">
                                <!-- Profile Link -->
                                <li>
                                    <a href="{{ route('profile') }}"
                                        class="block px-4 py-2 w-full text-sm hover:bg-gray-700 rounded-md transition">Lihat
                                        Profil</a>
                                </li>
                                <!-- Logout -->
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-700 rounded-md transition">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="grow bg-blue-600 text-white px-8 py-4 font-bold rounded-full text-sm hover:bg-blue-700 transition">Login</a>
                    <a href="{{ route('register') }}"
                        class="grow bg-blue-800 text-white px-8 py-4 font-bold rounded-full text-sm hover:bg-blue-700 transition">Sign
                        Up</a>
                @endauth
            </div>



            <div class="hidden lg:block order-2">
                <ul class="flex gap-16">
                    <li
                        class="{{ request()->is('/') ? 'text-blue-500 font-bold' : 'text-white hover:text-blue-600 font-semibold' }}">
                        <a href="/">Home</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" @click="searchOpen = true"
                            class="text-white font-semibold text-base hover:text-blue-600">Search</a>
                    </li>
                    <li
                        class="{{ request()->is('explore') ? 'text-blue-500 font-bold' : 'text-white hover:text-blue-600 font-semibold' }}">
                        <a href="/explore">Explore</a>
                    </li>
                    <li
                        class="{{ request()->is('posts') ? 'text-blue-500 font-bold' : 'text-white hover:text-blue-600 font-semibold' }}">
                        <a href="/posts">Posts</a>
                    </li>
                </ul>
            </div>
        </div>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let lastScrollTop = 0;
                const navbar = document.getElementById("navbar");

                window.addEventListener("scroll", function() {
                    let scrollTop = window.scrollY || document.documentElement.scrollTop;
                    if (scrollTop > lastScrollTop) {
                        // Scroll ke bawah -> sembunyikan navbar
                        navbar.style.transform = "translateY(-100%)";
                    } else {
                        // Scroll ke atas -> tampilkan navbar
                        navbar.style.transform = "translateY(0)";
                    }
                    lastScrollTop = scrollTop;
                });
            });
        </script>
        <style>
            #navbar {
                transition: transform 0.3s ease-in-out;
            }
        </style>
    </nav>

    <nav x-data="{
        navOpen: true,
    }">

        <!-- Navbar Bottom -->
        <div x-show="navBottomVisible"
            class="lg:hidden fixed bottom-5 left-1/2 transform -translate-x-1/2 w-[90%] max-w-md rounded-full bg-blue-600/90 backdrop-blur-lg shadow-xl border border-white/20 z-50 p-3 flex justify-around items-center"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-10">

            <!-- Indikator Aktif -->
            <div class="absolute bottom-[0.5px] h-1 w-10 bg-white rounded-full transition-all duration-300"
                :style="{
                    left: (window.location.pathname === '/' ? 'calc(15% - 20px)' :
                        window.location.pathname === '/explore' ? 'calc(39% - 20px)' :
                        window.location.pathname === '/posts' ? 'calc(62% - 20px)' :
                        window.location.pathname === '/profile' ? 'calc(85% - 20px)' :
                        'calc(300% - 0px)')
                }">
            </div>

            <!-- Home -->
            <a href="/" class="flex flex-col items-center gap-1 group relative">
                <ion-icon name="home"
                    :class="window.location.pathname === '/' ? 'text-white scale-110' :
                        'text-white/70 group-hover:text-white transition-all'"
                    class="text-2xl transition-all duration-300"></ion-icon>
                <span
                    :class="window.location.pathname === '/' ? 'text-white font-bold' :
                        'text-white/70 group-hover:text-white'"
                    class="text-xs transition-all">Home</span>
            </a>

            <!-- Explore -->
            <a href="/explore" class="flex flex-col items-center gap-1 group relative">
                <ion-icon name="compass"
                    :class="window.location.pathname === '/explore' ? 'text-white scale-110' :
                        'text-white/70 group-hover:text-white transition-all'"
                    class="text-2xl transition-all duration-300"></ion-icon>
                <span
                    :class="window.location.pathname === '/explore' ? 'text-white font-bold' :
                        'text-white/70 group-hover:text-white'"
                    class="text-xs transition-all">Explore</span>
            </a>

            <!-- Tombol Tengah Search -->
            <button @click="searchOpen = true"
                class="absolute -top-6 bg-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg border-4 border-blue-500 text-blue-500 transition-all hover:bg-blue-700 hover:text-white hover:border-blue-700">
                <ion-icon name="search" class="text-3xl"></ion-icon>
            </button>

            <!-- Posts -->
            <a href="/posts" class="flex flex-col items-center gap-1 group relative">
                <ion-icon name="create"
                    :class="window.location.pathname === '/posts' ? 'text-white scale-110' :
                        'text-white/70 group-hover:text-white transition-all'"
                    class="text-2xl transition-all duration-300"></ion-icon>
                <span
                    :class="window.location.pathname === '/posts' ? 'text-white font-bold' :
                        'text-white/70 group-hover:text-white'"
                    class="text-xs transition-all">Posts</span>
            </a>

            <!-- Profile (Hanya untuk user yang sudah login) -->
            @auth
                <a href="/profile" class="flex flex-col items-center gap-1 group relative">
                    <ion-icon name="person"
                        :class="window.location.pathname === '/profile' ? 'text-white scale-110' :
                            'text-white/70 group-hover:text-white transition-all'"
                        class="text-2xl transition-all duration-300"></ion-icon>
                    <span
                        :class="window.location.pathname === '/profile' ? 'text-white font-bold' :
                            'text-white/70 group-hover:text-white'"
                        class="text-xs transition-all">Profile</span>
                </a>
            @endauth

            <!-- More (Hanya untuk user yang belum login) -->
            @guest
                <button @click="modalOpen = true" class="flex flex-col items-center gap-1 group relative">
                    <ion-icon name="apps"
                        class="text-2xl text-white/70 group-hover:text-white transition-all"></ion-icon>
                    <span class="text-xs text-white/70 group-hover:text-white transition-all">More</span>
                </button>
            @endguest
        </div>

        <!-- Search Modal -->
        <div x-show="searchOpen"
            class="fixed inset-0 bg-gradient-to-b from-gray-900/80 to-black/80 backdrop-blur-md z-30 flex items-center justify-center"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Modal Content -->
            <div @click.away="searchOpen = false"
                class="bg-gray-800 p-6 rounded-2xl w-full max-w-md shadow-lg transform transition-all duration-300"
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

                <!-- Input Box -->
                <input type="text" id="searchInput" placeholder="Search movies, series, and more..."
                    x-model="query" @input.debounce.300ms="performSearch"
                    class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400 text-lg">

                <!-- Results Container -->
                <div x-show="results.length > 0" x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2"
                    class="mt-4 bg-gray-900 text-white shadow-inner max-h-60 overflow-auto scrollbar-hidden rounded-lg">

                    <template x-for="result in results" :key="result.id">
                        <a :href="'/detail/' + result.type + '/' + result.id"
                            class="flex items-center p-4 hover:bg-gray-700 transition duration-200 rounded-md">

                            <img :src="result.poster_path" alt="Poster"
                                class="w-16 h-24 object-cover mr-4 rounded-md">

                            <div>
                                <p class="font-bold text-lg" x-text="result.title"></p>
                                <p class="text-sm text-gray-400" x-text="result.release_date"></p>
                                <p class="text-sm text-yellow-400 font-semibold">
                                    <span x-text="'Rating: ' + result.average_rating"></span>
                                </p>
                            </div>
                        </a>
                    </template>



                </div>
            </div>
        </div>

        <!-- Modal More (Login & Sign Up) -->
        <div x-show="modalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modalOpen = false">

            <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-80 text-center" @click.stop>
                <!-- Mencegah klik di dalam modal menutupnya -->
                <h2 class="text-white text-lg font-semibold mb-4">Welcome!</h2>
                <p class="text-gray-400 text-sm mb-6">Login or sign up to access more features.</p>
                <a href="{{ route('login') }}"
                    class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mb-3 transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="block bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    Sign Up
                </a>
            </div>
        </div>
    </nav>
</nav>
