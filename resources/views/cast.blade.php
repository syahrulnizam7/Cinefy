@extends('layouts.app')

@section('content')
    <div class="relative bg-gray-900 text-white min-h-screen">
        <div class="relative">
            <!-- Background Gambar Profil dengan Gradien -->
            <div class="absolute inset-0 w-full h-full bg-black/40"
                style="background-image: url('{{ $castDetail['profile_path'] ? 'https://image.tmdb.org/t/p/w500' . $castDetail['profile_path'] : '/images/default-profile.jpg' }}');
            background-size: cover;
            background-position: top;">
            </div>
            <div class="absolute inset-0 backdrop-blur-md md:backdrop-blur-lg"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 via-[20%] to-transparent"></div>

            <!-- Konten Utama -->
            <div
                class="relative w-full mx-auto px-4 py-20 lg:py-32 lg:px-20 flex flex-col lg:flex-row gap-6 md:gap-10 min-h-screen">
                <div class="w-full lg:w-1/3 flex flex-col justify-between items-center relative h-full">
                    <!-- Profile Picture -->
                    <div class="relative w-full max-w-[220px] md:max-w-[250px] lg:max-w-none h-auto">
                        <div class="relative rounded-lg shadow-lg overflow-hidden">
                            <img src="{{ $castDetail['profile_path'] ? 'https://image.tmdb.org/t/p/w500' . $castDetail['profile_path'] : '/images/default-profile.jpg' }}"
                                alt="{{ $castDetail['name'] }}" class="w-full h-auto object-cover rounded-lg">

                            <!-- Overlay Gradasi -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Sosial Media di depan bagian bawah profile path -->
                    <div
                        class="absolute bottom-5 left-1/2 -translate-x-1/2 translate-y-1/2 flex items-center gap-4 px-4 py-2 rounded-full shadow-lg z-10">
                        @if ($socialMedia['twitter_id'])
                            <a href="https://twitter.com/{{ $socialMedia['twitter_id'] }}" target="_blank"
                                class="text-blue-400 hover:text-blue-600">
                                <ion-icon name="logo-twitter" class="text-3xl"></ion-icon>
                            </a>
                        @endif
                        @if ($socialMedia['instagram_id'])
                            <a href="https://instagram.com/{{ $socialMedia['instagram_id'] }}" target="_blank"
                                class="text-pink-400 hover:text-pink-600">
                                <ion-icon name="logo-instagram" class="text-3xl"></ion-icon>
                            </a>
                        @endif
                        @if ($socialMedia['facebook_id'])
                            <a href="https://facebook.com/{{ $socialMedia['facebook_id'] }}" target="_blank"
                                class="text-blue-800 hover:text-blue-900">
                                <ion-icon name="logo-facebook" class="text-3xl"></ion-icon>
                            </a>
                        @endif
                        @if ($socialMedia['youtube_id'])
                            <a href="https://youtube.com/{{ $socialMedia['youtube_id'] }}" target="_blank"
                                class="text-red-600 hover:text-red-800">
                                <ion-icon name="logo-youtube" class="text-3xl"></ion-icon>
                            </a>
                        @endif
                        @if ($socialMedia['tiktok_id'])
                            <a href="https://tiktok.com/{{ $socialMedia['tiktok_id'] }}" target="_blank"
                                class="text-black hover:text-gray-700">
                                <ion-icon name="logo-tiktok" class="text-3xl"></ion-icon>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Informasi Cast -->
                <div class="w-full lg:w-2/3 flex flex-col">
                    <h1 class="text-4xl md:text-5xl font-extrabold">{{ $castDetail['name'] }}</h1>
                    <p class="mt-4 text-gray-300 leading-relaxed">
                        {{ $castDetail['biography'] ?: 'Biography is not available.' }}
                    </p>

                    <!-- Peran Terkenal -->
                    <div class="mt-10">
                        <h2 class="text-2xl md:text-3xl font-semibold mb-4">Peran Terkenal</h2>
                        <div class="flex overflow-x-auto gap-4 snap-x snap-mandatory scrollbar-hidden">
                            @foreach ($credits as $credit)
                                <div class="flex-none w-36 sm:w-40">
                                    <a
                                        href="{{ route('detail', ['type' => $credit['media_type'], 'id' => $credit['id']]) }}">
                                        <img src="{{ $credit['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $credit['poster_path'] : asset('images/noimg.png') }}"
                                            alt="{{ $credit['title'] ?? $credit['name'] }}"
                                            class="w-full h-52 object-cover rounded-lg shadow-md hover:shadow-xl transition">

                                        <p class="text-sm mt-2 text-center">{{ $credit['title'] ?? $credit['name'] }}</p>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten Tambahan -->
            <div class=" mx-auto px-4 lg:px-20 py-10 relative z-10 bg-gray-900">
                <!-- About Section -->
                <div class="mt-10">
                    <h2 class="text-2xl md:text-3xl font-semibold mb-4">Tentang</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm md:text-base">
                        <div>
                            <p><strong>Stage Name:</strong> {{ $castDetail['name'] }}</p>
                            <p><strong>Peran:</strong> {{ $castDetail['known_for_department'] }}</p>
                            <p><strong>Reputasi:</strong> {{ $castDetail['popularity'] }}</p>
                        </div>
                        <div>
                            <p><strong>Jenis Kelamin:</strong> {{ $castDetail['gender'] == 1 ? 'Perempuan' : 'Laki-laki' }}
                            </p>
                            <p><strong>Tanggal Lahir:</strong> {{ $castDetail['birthday'] ?? 'Unknown' }}</p>
                            <p><strong>Lokasi Lahir:</strong> {{ $castDetail['place_of_birth'] ?? 'Unknown' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-2xl md:text-3xl font-semibold mb-4">Riwayat Perfilman</h2>

                    <!-- Toggle Kategori -->
                    <div class="flex gap-4 mb-6">
                        <button id="toggleMovies"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Movies</button>
                        <button id="toggleTvShows"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700">TV Shows</button>
                    </div>

                    <div class="relative border-l-4 border-blue-600 pl-6" id="timeline">
                        @foreach ($credits as $credit)
                            <div class="mb-8 relative" data-type="{{ $credit['media_type'] }}">
                                <div class="absolute left-[-12px] w-6 h-6 bg-blue-600 rounded-full"></div>
                                <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
                                    <p class="text-sm text-gray-400">
                                        {{ substr($credit['release_date'] ?? $credit['first_air_date'], 0, 4) ?? 'Unknown' }}
                                    </p>
                                    <h3 class="text-lg font-semibold">
                                        <a href="{{ route('detail', ['type' => $credit['media_type'], 'id' => $credit['id']]) }}"
                                            class="text-blue-400 hover:underline">
                                            {{ $credit['title'] ?? $credit['name'] }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-300">{{ $credit['character'] ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <script>
                    document.getElementById('toggleMovies').addEventListener('click', function() {
                        filterTimeline('movie');
                    });

                    document.getElementById('toggleTvShows').addEventListener('click', function() {
                        filterTimeline('tv');
                    });

                    function filterTimeline(type) {
                        document.querySelectorAll('#timeline > div').forEach(item => {
                            if (item.getAttribute('data-type') === type) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }
                </script>

            </div>
        </div>
    </div>
@endsection
