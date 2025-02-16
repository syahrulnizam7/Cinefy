@extends('layouts.app')



@section('content')
<div
    class="fixed top-40 -left-52 md:top-52 lg:top-80 lg:-left-40 w-72 h-72 md:w-[400px] md:h-[400px] bg-green-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-green-500/50 -z-10 animate-moveCircle1">
</div>
<div
    class="fixed -top-32 -right-40 md:-top-44 md:-right-56 w-72 h-72 md:w-[420px] md:h-[420px] bg-pink-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-pink-500/50 -z-10 animate-moveCircle2">
</div>
    <div class=" mt-16 mx-auto lg:mt-16 px-4 py-8 text-white relative">
        <div class="flex items-center justify-center gap-4">
            <div class="flex-1 h-[2px] bg-slate-400"></div>
            <h1 class="text-2xl font-semibold text-white whitespace-nowrap">
                Explore Your Next Watch
            </h1>
            <div class="flex-1 h-[2px] bg-slate-400"></div>
        </div>

        <!-- Filter Modal with Alpine.js -->
        <div x-data="{ open: false, isBottomVisible: false }" @toggle-filter-position.window="isBottomVisible = !isBottomVisible">

            <!-- Floating Filter Button -->
            <button @click="open = true" :class="isBottomVisible ? 'bottom-6' : 'lg:bottom-6 bottom-28'"
                class="fixed right-6 hover:bg-blue-600 bg-blue-500 text-white px-5 py-3 rounded-full shadow-lg transition-all z-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 inline-block" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-5.414 5.414a1 1 0 00-.293.707v4.586l-4 2V13.12a1 1 0 00-.293-.707L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                Filter
            </button>
            <!-- Overlay (Background) -->
            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-md z-30">
            </div>

            <div x-show="open" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="fixed inset-0 flex items-center justify-center z-30 transition-all">
                <div class="bg-gray-900 text-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
                    <!-- Close Button -->
                    <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-white transition">
                        ✕
                    </button>

                    <h2 class="text-xl font-semibold mb-4">Filter</h2>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('explore.index') }}" class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="text-sm text-gray-400">Genre</label>
                            <select name="genre"
                                class="p-2 w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-300">
                                <option value="">All Genres</option>
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre['id'] }}"
                                        {{ request('genre') == $genre['id'] ? 'selected' : '' }}>
                                        {{ $genre['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Year</label>
                            <input type="number" name="year" min="1900" max="{{ date('Y') }}"
                                value="{{ request('year') }}"
                                class="p-2 w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-300"
                                placeholder="Enter year">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Min Rating</label>
                            <input type="number" name="rating" min="0" max="10" step="0.1"
                                value="{{ request('rating') }}"
                                class="p-2 w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-300"
                                placeholder="Min rating">
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Type</label>
                            <select name="type"
                                class="p-2 w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-300">
                                <option value="" {{ request('type') == '' ? 'selected' : '' }}>All</option>
                                <option value="movie" {{ request('type') == 'movie' ? 'selected' : '' }}>Movies</option>
                                <option value="tv" {{ request('type') == 'tv' ? 'selected' : '' }}>TV Shows</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-gray-400">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="p-2 w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-300"
                                placeholder="Search by title" autocomplete="off">
                        </div>

                        <!-- Apply Button -->
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-500 transition-all px-4 py-2 rounded-lg text-white font-semibold shadow-lg">
                            Apply
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <!-- Autocomplete Results -->
        <div id="autocomplete-results"
            class="absolute bg-gray-800 text-white w-full mt-1 rounded-md shadow-md hidden max-h-60 overflow-y-auto z-10">
        </div>

        <!-- Movies/TV Shows List (Responsive Poster) -->
        <div id="movies-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-8">
            @foreach ($items as $index => $item)
                <a href="{{ route('detail', ['type' => $item['media_type'] ?? $mediaType, 'id' => $item['id']]) }}"
                    class="group block bg-gray-800 rounded-lg overflow-hidden shadow-lg transform transition-all duration-300 hover:shadow-xl relative hover:brightness-110 opacity-0 animate-fadeIn"
                    style="animation-delay: {{ $index * 100 }}ms">

                    <!-- Poster dengan rasio 2:3 -->
                    <div class="relative w-full aspect-[2/3] bg-gray-700">
                        <img src="{{ $item['poster_path'] ? 'https://image.tmdb.org/t/p/w200' . $item['poster_path'] : asset('/images/noimg.png') }}"
                            alt="{{ $item['title'] ?? $item['name'] }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-all group-hover:brightness-125">
                    </div>

                    <div class="p-2 absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black to-transparent">
                        <h2 class="text-sm font-bold text-white truncate">
                            {{ $item['title'] ?? $item['name'] }}
                        </h2>
                        <p class="text-xs opacity-70 mt-1 text-white">
                            ⭐ {{ $item['vote_average'] ?? 'N/A' }}/10 | 📅
                            {{ $item['release_date'] ?? ($item['first_air_date'] ?? 'Unknown') }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Load More Button -->
        <div class="flex justify-center mt-8">
            <button id="load-more-btn"
                class="px-4 py-2 sm:px-6 sm:py-3 md:px-8 md:py-3 bg-blue-600 text-white font-semibold rounded-lg 
        hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 
        transition-all duration-300 ease-in-out shadow-lg w-full sm:w-auto">
                Load More
            </button>
        </div>

        <script>
            const openFilterModal = document.getElementById('openFilterModal');
            const closeFilterModal = document.getElementById('closeFilterModal');
            const filterModal = document.getElementById('filterModal');

            // Open Modal
            openFilterModal.addEventListener('click', () => {
                filterModal.classList.remove('hidden');
                filterModal.classList.add('flex');
            });

            // Close Modal
            closeFilterModal.addEventListener('click', () => {
                filterModal.classList.add('hidden');
                filterModal.classList.remove('flex');
            });

            // Close on outside click
            window.addEventListener('click', (e) => {
                if (e.target === filterModal) {
                    filterModal.classList.add('hidden');
                    filterModal.classList.remove('flex');
                }
            });
        </script>

        <script>
            let page = {{ $page }};
            let loading = false;

            document.getElementById('load-more-btn').addEventListener('click', function() {
                if (loading) return;
                loading = true;

                let filters = new URLSearchParams(window.location.search);
                filters.set('page', page + 1);

                let url =
                    "{{ route('explore.index', ['page' => '__page__', 'search' => request('search'), 'genre' => request('genre'), 'year' => request('year'), 'rating' => request('rating'), 'type' => request('type')]) }}";
                url = url.replace('__page__', page + 1);

                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        let grid = document.getElementById('movies-list');
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(data, 'text/html');
                        let newItems = doc.querySelectorAll('#movies-list > a');

                        if (newItems.length === 0) {
                            document.getElementById('load-more-btn').style.display = 'none';
                            return;
                        }

                        newItems.forEach((item, index) => {
                            item.classList.add('opacity-0');
                            grid.appendChild(item);

                            // Gunakan requestAnimationFrame untuk memicu animasi
                            requestAnimationFrame(() => {
                                item.classList.add('animate-fadeIn');
                                item.style.animationDelay = `${index * 100}ms`;
                                item.classList.remove('opacity-0');
                            });
                        });

                        page++;
                        loading = false;
                    })
                    .catch(error => {
                        console.error('Error loading more items:', error);
                        loading = false;
                    });
            });



            // Autocomplete Search
            document.getElementById('search').addEventListener('input', function() {
                let query = this.value;
                if (query.length < 3) {
                    document.getElementById('autocomplete-results').classList.add('hidden');
                    return;
                }

                fetch(`/api/search?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        let results = data.results || [];
                        let resultsContainer = document.getElementById('autocomplete-results');
                        resultsContainer.innerHTML = '';

                        results.forEach(item => {
                            let div = document.createElement('div');
                            div.classList.add('px-4', 'py-2', 'hover:bg-gray-700', 'cursor-pointer');
                            div.textContent = item.title;
                            div.addEventListener('click', function() {
                                document.getElementById('search').value = item.title;
                                resultsContainer.classList.add('hidden');
                            });
                            resultsContainer.appendChild(div);
                        });

                        // Show results below the search input
                        resultsContainer.classList.remove('hidden');
                    })
                    .catch(error => console.error('Error fetching autocomplete results:', error));
            });

            // Hide autocomplete when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#search') && !event.target.closest('#autocomplete-results')) {
                    document.getElementById('autocomplete-results').classList.add('hidden');
                }
            });
        </script>
    </div>
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-in-out forwards;
        }
    </style>
@endsection
