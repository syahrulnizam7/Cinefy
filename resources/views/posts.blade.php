@extends('layouts.app')

@section('content')
    
<div
    class="fixed top-40 -left-52 md:top-52 lg:top-80 lg:-left-40 w-72 h-72 md:w-[400px] md:h-[400px] bg-green-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-green-500/50 -z-10 animate-moveCircle1">
</div>
<div
    class="fixed -top-32 -right-40 md:-top-44 md:-right-56 w-72 h-72 md:w-[420px] md:h-[420px] bg-pink-400 rounded-full blur-3xl opacity-50 shadow-lg shadow-pink-500/50 -z-10 animate-moveCircle2">
</div>
    @php
        use Illuminate\Support\Str;

        use Illuminate\Support\Facades\Http;

        function getOverviewFromApi($type, $id)
        {
            $apiKey = config('services.tmdb.api_key');

            // Coba ambil overview dalam bahasa Indonesia
            $response = Http::get("https://api.themoviedb.org/3/{$type}/{$id}", [
                'api_key' => $apiKey,
                'language' => 'id-ID',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['overview'])) {
                    return $data['overview']; // Jika overview ada dalam bahasa Indonesia, gunakan
                }
            }

            // Jika overview dalam bahasa Indonesia kosong, ambil dalam bahasa Inggris
            $responseEn = Http::get("https://api.themoviedb.org/3/{$type}/{$id}", [
                'api_key' => $apiKey,
                'language' => 'en-US',
            ]);

            if ($responseEn->successful()) {
                $dataEn = $responseEn->json();
                return $dataEn['overview'] ?? 'Deskripsi tidak tersedia.';
            }

            return 'Deskripsi tidak tersedia.';
        }

    @endphp

    <div class="max-w-3xl mx-auto p-6 mt-10">

        <div x-data="{ showNotification: false, message: '' }" x-show="showNotification" x-transition.duration.500ms
            class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            <p x-text="message"></p>
        </div>


        <!-- List Posts -->
        <div class="space-y-8">
            @foreach ($posts as $post)
                <div x-data="{ openDropdown: false }" class="border-b border-gray-700 p-6  shadow-md ">

                    <!-- User Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('user.detail', $post->user->id) }}">
                                <img src="{{ $post->user->profile_photo ? $post->user->profile_photo : 'https://res.cloudinary.com/dj2pofe14/image/upload/images/noimg.png' }}"
                                    alt="Avatar" class="w-12 h-12 rounded-full border-2 border-blue-500 object-cover">

                            </a>
                            <div>
                                <a href="{{ route('user.detail', $post->user->id) }}"
                                    class="font-bold text-white text-lg hover:text-blue-400">{{ $post->user->name }}</a>
                                <p class="text-sm text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Menu Titik Tiga -->
                        <div class="relative ml-auto">
                            <button @click="openDropdown = !openDropdown"
                                class="text-gray-400 hover:text-white focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="3.5" stroke="currentColor" class="w-6 h-6 ">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 12h.008m5.992 0h.008m5.992 0h.008M6.75 12h.008m5.992 0h.008m5.992 0h.008" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="openDropdown" @click.away="openDropdown = false"
                                class="absolute right-0 mt-2 w-40 bg-gray-800 border border-gray-700 rounded-md shadow-lg z-50">
                                <a href="{{ route('user.detail', $post->user->id) }}"
                                    class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                    About This User
                                </a>

                                @if (auth()->id() === $post->user_id)
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-700">
                                            Delete Post
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>


                    <!-- Post Content -->
                    <div class="mt-2 text-gray-300">
                        @if ($post->images->count() > 0)
                            @php
                                $sharedType = null;
                                foreach ($post->images as $image) {
                                    $imageData = json_decode($image->image_path, true);
                                    if (
                                        is_array($imageData) &&
                                        isset($imageData['type']) &&
                                        in_array($imageData['type'], ['movie', 'tv'])
                                    ) {
                                        $sharedType = $imageData['type'] === 'movie' ? 'Movie' : 'TV Show';
                                        break;
                                    }
                                }
                            @endphp

                            @if ($sharedType)
                                <p class="text-sm text-gray-400 mb-2">📢 Shared a {{ $sharedType }}</p>
                            @endif
                        @endif

                        <p>{{ $post->content }}</p>

                        @if ($post->images->count() > 0)
                            @foreach ($post->images as $image)
                                @php
                                    // Cek apakah data disimpan dalam bentuk JSON
                                    $imageData = json_decode($image->image_path, true);

                                    if (is_array($imageData) && isset($imageData['image'])) {
                                        $imageUrl = $imageData['image'];
                                        $type = $imageData['type'] ?? null;
                                        $movieId = $imageData['id'] ?? null;
                                        $title = $imageData['title'] ?? 'Unknown';
                                        $voteAverage = $imageData['vote_average'] ?? 'N/A';
                                        $releaseDate = $imageData['release_date'] ?? 'Unknown';
                                        $overview = getOverviewFromApi($type, $movieId); // Ambil overview dari API
                                    } else {
                                        $imageUrl = filter_var($image->image_path, FILTER_VALIDATE_URL)
                                            ? $image->image_path
                                            : asset('storage/' . $image->image_path);
                                        $type = null;
                                        $movieId = null;
                                        $title = null;
                                        $voteAverage = null;
                                        $releaseDate = null;
                                        $overview = null;
                                    }
                                @endphp

                                @if ($type && $movieId)
                                    <a href="{{ route('detail', ['type' => $type, 'id' => $movieId]) }}" class="mt-3 block">
                                        <div
                                            class="bg-gray-900 border border-gray-800 p-6 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                                                <!-- Gambar -->
                                                <div class="relative">
                                                    <img src="{{ $imageUrl }}" alt="Post Image"
                                                        class="w-full max-h-80 object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105">


                                                </div>

                                                <!-- Informasi -->
                                                <div class="text-white">
                                                    <h2 class="text-2xl font-bold mb-1">{{ $title }}</h2>
                                                    <p class="text-sm text-gray-300">⭐ {{ $voteAverage }} | 📅
                                                        {{ $releaseDate }}</p>

                                                    <!-- Tampilkan Genre -->
                                                    @if (!empty($imageData['genres']))
                                                        <div class="flex flex-wrap gap-1 mt-2">
                                                            @foreach ($imageData['genres'] as $genre)
                                                                <span
                                                                    class="bg-red-600 text-white px-2 py-1 text-xs rounded-full">{{ $genre }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <p class="text-sm mt-3 text-gray-300 leading-relaxed">
                                                        {{ $overview }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <!-- Jika bukan dari detail, tampilkan biasa -->
                                    <img src="{{ $image->image_path }}" alt="Post Image"
                                        class="mt-3 w-full max-h-96 object-cover rounded-lg shadow-md">
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Post Actions -->
                    <div class="flex items-center justify-between mt-4 text-gray-400">
                        <!-- Like Button with Alpine.js -->
                        <div x-data="likeComponent({{ $post->id }}, {{ $post->isLikedBy(auth()->id()) ? 'true' : 'false' }}, {{ $post->likes->count() }})" data-post-id="{{ $post->id }}">
                            <button @click="toggleLike" class="flex items-center space-x-1"
                                :class="liked ? 'text-red-500' : 'text-gray-500'">
                                <svg xmlns="http://www.w3.org/2000/svg" :fill="liked ? 'red' : 'none'" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.828 9.172a4 4 0 015.657 5.656l-6.343 6.343a1 1 0 01-1.414 0l-6.343-6.343a4 4 0 115.657-5.656z" />
                                </svg>
                                <span x-text="likeCount"></span>
                            </button>
                        </div>

                        <!-- Comments -->
                        <button class="flex items-center space-x-1 hover:text-blue-500"
                            onclick="toggleComments('{{ $post->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 12h9M9 15h6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $post->comments->count() }}</span>
                        </button>
                    </div>

                    <!-- Comments Section -->
                    <div id="comments-{{ $post->id }}" class="hidden mt-4">
                        <form action="{{ route('comments.store', $post->id) }}" method="POST"
                            class="relative mb-4 p-4 rounded-lg backdrop-blur-lg bg-gray-900/80 border border-gray-700 shadow-lg">
                            @csrf
                            <div class="relative">
                                <textarea name="content" rows="2"
                                    class="w-full p-3 pr-12 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none resize-none"
                                    placeholder="Type your opinion"></textarea>
                                <button type="submit"
                                    class="absolute bottom-3 right-3 bg-blue-600 hover:bg-blue-700 text-white m-auto w-10 h-10 flex items-center justify-center rounded-full shadow-md transition">
                                    <ion-icon name="paper-plane"></ion-icon>
                                </button>

                            </div>
                        </form>

                        <div class="space-y-3">
                            @foreach ($post->comments as $comment)
                                <div
                                    class="flex items-start space-x-3 bg-gray-800 p-3 rounded-lg border border-gray-700 relative">
                                    <a href="{{ route('user.detail', $comment->user->id) }}">
                                        <img src="{{ $comment->user->profile_photo ? $comment->user->profile_photo : 'https://res.cloudinary.com/dj2pofe14/image/upload/images/noimg.png' }}"
                                            alt="Avatar"
                                            class="w-10 h-10 rounded-full border border-gray-600 object-cover">

                                    </a>
                                    <div class="flex-1">
                                        <a href="{{ route('user.detail', $comment->user->id) }}"
                                            class="font-semibold text-white hover:text-blue-400">
                                            {{ $comment->user->name }}
                                        </a>
                                        <p class="text-gray-300" id="comment-text-{{ $comment->id }}">
                                            {{ $comment->content }}</p>
                                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>

                                    <!-- Jika komentar milik user, tampilkan tombol titik tiga -->
                                    @if (Auth::id() === $comment->user_id)
                                        <div class="relative">
                                            <button onclick="toggleOptions({{ $comment->id }})"
                                                class="text-white hover:bg-gray-700 p-2 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"
                                                    class="w-6 h-6 ">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6.75 12h.008m5.992 0h.008m5.992 0h.008M6.75 12h.008m5.992 0h.008m5.992 0h.008" />
                                                </svg>
                                            </button>
                                            <div id="options-{{ $comment->id }}"
                                                class="hidden absolute right-0 mt-2 w-32 bg-gray-900 border border-gray-700 rounded-lg shadow-lg">
                                                <button onclick="editComment({{ $comment->id }})"
                                                    class="w-full text-left px-4 py-2 text-white hover:bg-gray-700">✏
                                                    Edit</button>
                                                <form action="{{ route('comments.destroy', $comment->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-red-400 hover:bg-gray-700">🗑
                                                        Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Modal Edit Komentar -->
                                <div id="edit-modal-{{ $comment->id }}"
                                    class="hidden fixed inset-0 flex items-center justify-center bg-black/50">
                                    <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
                                        <h2 class="text-white font-semibold mb-4">Edit Komentar</h2>
                                        <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="content" id="edit-content-{{ $comment->id }}"
                                                class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 text-white" rows="3">{{ $comment->content }}</textarea>
                                            <div class="mt-4 flex justify-end space-x-2">
                                                <button type="button" onclick="closeEditModal({{ $comment->id }})"
                                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg">Batal</button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <script>
                        function toggleOptions(commentId) {
                            document.getElementById('options-' + commentId).classList.toggle('hidden');
                        }

                        function editComment(commentId) {
                            document.getElementById('edit-modal-' + commentId).classList.remove('hidden');
                            document.getElementById('options-' + commentId).classList.add('hidden');
                        }

                        function closeEditModal(commentId) {
                            document.getElementById('edit-modal-' + commentId).classList.add('hidden');
                        }
                    </script>


                </div>
            @endforeach
        </div>
    </div>

    <div x-data="{ showMoreModal: false, showPostModal: false, isBottomVisible: false }" @toggle-filter-position.window="isBottomVisible = !isBottomVisible">

        <!-- Floating Button -->
        <button @click="
        @guest showMoreModal = true; 
        @else showPostModal = true; @endguest"
            :class="isBottomVisible ? 'bottom-6' : 'lg:bottom-6 bottom-28'"
            class="fixed right-6 hover:bg-blue-600 bg-blue-500 text-white p-4 rounded-full shadow-lg transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>

        <!-- Modal More (Untuk User Belum Login) -->
        @guest
            <div x-show="showMoreModal" x-cloak @click.away="showMoreModal = false"
                class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-80">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-80 text-center" @click.stop>
                    <!-- Mencegah klik di dalam modal menutupnya -->
                    <h2 class="text-white text-lg font-semibold mb-4">You Are Not Logged In</h2>
                    <p class="text-gray-400 text-sm mb-6">Please log in to create a post.</p>
                    <a href="{{ route('login') }}"
                        class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mb-3 transition">
                        Login
                    </a>
                    <button @click="showMoreModal = false"
                        class="w-full block bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Later</button>
                    </a>
                </div>
            </div>
        @endguest

        <!-- Modal untuk Membuat Post (User Sudah Login) -->
        @auth
            <div x-show="showPostModal" x-cloak @click.away="showPostModal = false"
                class="z-20 fixed inset-0 flex items-center justify-center bg-black bg-opacity-60">
                <div class="bg-gray-900 text-white p-6 rounded-xl w-96 shadow-2xl border border-gray-700">
                    <h3 class="text-xl font-semibold mb-4">Create a New Post</h3>
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="content" rows="3"
                            class="w-full p-3 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:ring focus:ring-blue-500 focus:outline-none resize-none"
                            placeholder="Share your thoughts about a movie or series..." required></textarea>

                        <label class="block mt-4 text-gray-300">Upload Images</label>
                        <input type="file" name="images[]" multiple
                            class="mt-2 w-full text-sm text-gray-300 bg-gray-800 border border-gray-600 rounded-lg  file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-white file:bg-blue-600 hover:file:bg-blue-700">

                        <div class="mt-5 flex justify-end space-x-2">
                            <button type="button"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition"
                                @click="showPostModal = false">Cancel</button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition">
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @endauth
    </div>




    <script>
        function toggleModal() {
            const modal = document.getElementById('post-modal');
            modal.classList.toggle('hidden');
        }

        function toggleComments(postId) {
            const comments = document.getElementById(`comments-${postId}`);
            comments.classList.toggle('hidden');
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('likeComponent', (postId, initialLiked, initialLikeCount) => ({
                liked: initialLiked,
                likeCount: initialLikeCount,

                toggleLike() {
                    fetch(`/posts/${postId}/toggle-like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.liked = data.liked;
                            this.likeCount = data.likeCount;
                        })
                        .catch(error => console.error('Error:', error));
                }
            }));
        });
    </script>
@endsection
