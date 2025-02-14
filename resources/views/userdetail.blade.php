@extends('layouts.app')

@section('content')
    <div class="max-w-6xl lg:mt-10 mx-auto py-12 px-6 relative">
        <!-- Profile Section -->
        <div class="p-6 md:p-8 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-30 blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                <!-- Profile Picture -->
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-white">
                    <img src="{{ $user->profile_photo ?? 'https://res.cloudinary.com/your_cloud_name/image/upload/v1/profile_pictures/default-avatar.png' }}" 
     alt="{{ $user->name }}" 
     class="object-cover w-full h-full rounded-full border-2 border-gray-300">

                </div>

                <!-- User Info -->
                <div class="text-center md:text-left">
                    <h2 class="text-3xl font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-300"><span>@</span>{{ $user->username }}</p>
                    <div class="flex justify-center md:justify-start mt-4 space-x-6 text-white">
                        <div class="text-center">
                            <p class="text-lg font-bold">{{ $watchedCount }}</p>
                            <p class="text-sm text-gray-400">Watched</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold">{{ $watchlistCount }}</p>
                            <p class="text-sm text-gray-400">Watchlist</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold">{{ $favoriteCount }}</p>
                            <p class="text-sm text-gray-400">Favorites</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Icon (Dropdown) -->
                <div x-data="{ open: false }" class="absolute top-4 lg:top-0 right-4 md:relative md:ml-auto">
                    <button @click="open = !open" class="text-white text-2xl hover:text-gray-300 transition">
                        <ion-icon name="settings"></ion-icon>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-white hover:bg-gray-700">Edit
                            Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-white hover:bg-gray-700">Logout</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tabs Section -->
        <div x-data="{ tab: 'watched' }" class="mt-8">
            <!-- Tabs -->
            <div class="relative flex justify-center border-b border-gray-700 mb-6">
                <button @click="tab = 'watched'" class="relative px-4 py-2 text-white hover:text-blue-400 transition"
                    :class="{ 'text-blue-400': tab === 'watched' }">
                    Recently Watched
                </button>
                <button @click="tab = 'watchlist'" class="relative px-4 py-2 text-white hover:text-blue-400 transition"
                    :class="{ 'text-blue-400': tab === 'watchlist' }">
                    Watchlist
                </button>
                <button @click="tab = 'favorite'" class="relative px-4 py-2 text-white hover:text-blue-400 transition"
                    :class="{ 'text-blue-400': tab === 'favorite' }">
                    Favorite
                </button>

                <!-- Active Indicator -->
                <div class="absolute bottom-0 h-1 bg-blue-400 rounded-full transition-all duration-300"
                    :style="{
                        width: tab === 'watched' ? '150px' : tab === 'watchlist' ? '100px' : '80px',
                        transform: tab === 'watched' ? 'translateX(-95px)' : tab === 'watchlist' ? 'translateX(35px)' :
                            'translateX(125px)'
                    }">
                </div>
            </div>

            <!-- Watched Content -->
            <div x-show="tab === 'watched'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($watched as $item)
                    <div class="group relative cursor-pointer">
                        <a href="{{ route('detail', ['type' => $item->type, 'id' => $item->tmdb_id]) }}" class="block">
                            <!-- Poster -->
                            <img src="https://image.tmdb.org/t/p/w500{{ $item->poster_path }}"
                                class="rounded-lg shadow-md transform group-hover:scale-105 transition duration-300">
                            <p class="text-xs text-gray-400 mt-2 text-center font-semibold">{{ $item->title }}</p>
                        </a>

                        <!-- Rating pengguna -->
                        <div class="flex justify-center mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <ion-icon name="{{ $i <= $item->rating ? 'star' : 'star-outline' }}"
                                    class="text-yellow-400 text-lg"></ion-icon>
                            @endfor
                        </div>

                        <!-- Review Chat Bubble (Muncul saat hover) -->
                        @if ($item->review)
                            <div
                                class="absolute top-0 right-0 transform translate-x-3 -translate-y-3 opacity-0 group-hover:opacity-100 
                            transition-all duration-300">
                                <div class="bg-gray-900 text-white text-xs p-3 rounded-lg shadow-lg max-w-[200px] relative">
                                    <p class="italic">{{ $item->review }}</p>
                                    <!-- Chat Bubble Tail -->
                                    <div
                                        class="absolute top-full right-3 w-0 h-0 border-t-[10px] border-t-gray-900 border-x-[8px] 
                                    border-x-transparent">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>


            <!-- Watchlist Content -->
            <div x-show="tab === 'watchlist'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($watchlist as $item)
                    <div class="group">
                        <a href="{{ route('detail', ['type' => $item->type, 'id' => $item->tmdb_id]) }}" class="block">
                            <img src="https://image.tmdb.org/t/p/w500{{ $item->poster_path }}"
                                class="rounded-lg shadow-md transform group-hover:scale-105 transition">
                            <p class="text-xs text-gray-400 mt-2 text-center">{{ $item->title }}</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Favorite Content -->
            <div x-show="tab === 'favorite'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($favorite as $item)
                    <div class="group">
                        <a href="{{ route('detail', ['type' => $item->type, 'id' => $item->tmdb_id]) }}" class="block">
                            <img src="https://image.tmdb.org/t/p/w500{{ $item->poster_path }}"
                                class="rounded-lg shadow-md transform group-hover:scale-105 transition">
                            <p class="text-xs text-gray-400 mt-2 text-center">{{ $item->title }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
