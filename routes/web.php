<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Http\Controllers\CastController;
use App\Http\Controllers\WatchedController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('login/google', [LoginController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);

// POST routes
Route::resource('/posts', PostController::class);

// PROFILE routes (karena hanya show)
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
Route::get('/profile/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
// Route untuk menyimpan data profil
Route::post('/welcome/save', [ProfileController::class, 'saveProfile'])->name('welcome.save')->middleware('auth');

// Middleware untuk memastikan user mengisi profilnya sebelum bisa mengakses halaman utama
Route::middleware(['auth', 'profile.complete'])->group(function () {

    // Semua route yang membutuhkan profil lengkap
    // Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
});
Route::get('/user/{id}', [UserController::class, 'show'])->name('user.detail');

Route::get('/cast/{id}', [CastController::class, 'show'])->name('cast.show');


Route::get('/detail/{type}/{id}', function ($type, $id) {
    $apiKey = config('services.tmdb.api_key');

    if (!in_array($type, ['movie', 'tv'])) {
        abort(404);
    }

    // Ambil detail utama
    $detailResponse = Http::get(config('services.tmdb.base_url') . "{$type}/{$id}", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
    ]);

    if ($detailResponse->failed()) {
        // Menangani jika ada kesalahan API
        abort(500, 'Terjadi kesalahan dalam mengambil data detail.');
    }

    $detail = $detailResponse->json();


    // ✅ Ambil Watch Providers
    $providersResponse = Http::get(config('services.tmdb.base_url') . "{$type}/{$id}/watch/providers", [
        'api_key' => $apiKey,
    ]);
    $providers = $providersResponse->json();

    // ✅ Ambil provider yang tersedia di Indonesia by Justwatch
    $watchProviders = $providers['results']['ID'] ?? [];

    // Jika tagline atau overview kosong, coba ambil dari bahasa Inggris
    if (empty($detail['tagline']) || empty($detail['overview'])) {
        $detailResponseEn = Http::get(config('services.tmdb.base_url') . "{$type}/{$id}", [
            'api_key' => $apiKey,
            'language' => 'en-US',
        ]);
        $detailEn = $detailResponseEn->json();

        $detail['tagline'] = $detail['tagline'] ?: $detailEn['tagline'];
        $detail['overview'] = $detail['overview'] ?: $detailEn['overview'];
    }

    // Ambil informasi pemeran utama dan kru
    $creditsResponse = Http::get(config('services.tmdb.base_url') . "{$type}/{$id}/credits", [
        'api_key' => $apiKey,
    ]);
    $credits = $creditsResponse->json();

    // Ambil hanya 10 pemeran utama
    $cast = array_slice($credits['cast'], 0, 10);

    // Ambil sutradara & penulis dari crew
    $director = collect($credits['crew'])->firstWhere('job', 'Director');
    $writers = collect($credits['crew'])->whereIn('job', ['Writer', 'Screenplay'])->pluck('name')->toArray();

    // Ambil trailer video
    $videosResponse = Http::get(config('services.tmdb.base_url') . "{$type}/{$id}/videos", [
        'api_key' => $apiKey,
    ]);
    $videos = $videosResponse->json();

    // Ambil trailer key (jika ada)
    $trailerKey = null;
    if (isset($videos['results']) && count($videos['results']) > 0) {
        $trailer = collect($videos['results'])->firstWhere('type', 'Trailer');
        if ($trailer) {
            $trailerKey = $trailer['key'];
        }
    }

    return view('detail', compact('detail', 'cast', 'director', 'writers', 'trailerKey', 'type', 'watchProviders'));
})->name('detail');

// Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/', function () {
    $apiKey = config('services.tmdb.api_key');

    // Ambil daftar trending harian
    $trendingDayResponse = Http::get(config('services.tmdb.base_url') . "trending/all/day", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
    ]);
    $trendingDay = $trendingDayResponse->json()['results'] ?? [];

    // Ambil daftar trending mingguan
    $trendingWeekResponse = Http::get(config('services.tmdb.base_url') . "trending/all/week", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
    ]);
    $trendingWeek = $trendingWeekResponse->json()['results'] ?? [];

    // Ambil daftar film terbaru (now playing)
    $latestTrailersResponse = Http::get(config('services.tmdb.base_url') . "movie/now_playing", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
        'page' => 1
    ]);
    $latestTrailers = $latestTrailersResponse->json()['results'] ?? [];

    // Ambil video trailer untuk setiap film terbaru
    foreach ($latestTrailers as &$movie) {
        $videosResponse = Http::get(config('services.tmdb.base_url') . "movie/{$movie['id']}/videos", [
            'api_key' => $apiKey,
        ]);

        $videos = $videosResponse->json()['results'] ?? [];
        $trailer = collect($videos)->firstWhere('type', 'Trailer');
        $movie['trailer_key'] = $trailer ? $trailer['key'] : null;
    }

    // Ambil daftar Popular TV
    $popularTVResponse = Http::get(config('services.tmdb.base_url') . "tv/popular", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
        'page' => 1
    ]);
    $popularTV = $popularTVResponse->json()['results'] ?? [];

    // Ambil daftar Popular Movies
    $popularMoviesResponse = Http::get(config('services.tmdb.base_url') . "movie/popular", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
        'page' => 1
    ]);
    $popularMovies = $popularMoviesResponse->json()['results'] ?? [];

    $topUsers = DB::table('watched')
        ->select('users.id', 'users.username', 'users.name', 'users.profile_photo', DB::raw('COUNT(watched.id) as total_watched'))
        ->join('users', 'watched.user_id', '=', 'users.id')
        ->groupBy('users.id', 'users.username', 'users.name', 'users.profile_photo')
        ->orderByDesc('total_watched')
        ->limit(10)
        ->get();


    return view('home', compact('trendingDay', 'trendingWeek', 'latestTrailers', 'popularTV', 'popularMovies', 'topUsers'));
});



// Route untuk halaman welcome
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome')->withoutMiddleware([\App\Http\Middleware\EnsureProfileComplete::class]);


Route::get('/check-username', function (Request $request) {
    $exists = \App\Models\User::where('username', $request->username)->exists();
    return response()->json(['exists' => $exists]);
});
Route::post('/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
})->name('check.email');

Route::get('/login', function () {
    return view('login');
})->name('login');


Route::middleware(['auth'])->group(function () {
    Route::post('/watched', [WatchedController::class, 'store'])->name('watched.store');
    Route::get('/watched/check', [WatchedController::class, 'check'])->name('watched.index');
    Route::delete('/watched', [WatchedController::class, 'destroy'])->name('watched.destroy');

    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::delete('/watchlist', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    Route::post('/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::get('/favorite/check', [FavoriteController::class, 'check'])->name('favorite.index');
    Route::delete('/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');


    // Route::get('/profile/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
    // Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::post('/posts/{post}/toggle-like', [LikeController::class, 'toggleLike'])->name('likes.toggle');

    Route::post('/posts/{post}/comment', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');
});


Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', function () {
    Auth::logout();
    Session::invalidate(); // Bersihkan sesi
    Session::regenerateToken(); // Regenerasi token CSRF untuk meningkatkan keamanan
    return redirect('/'); // Arahkan pengguna setelah logout
})->name('logout');


Route::get('/api/search', function () {
    $query = request('q');
    if (!$query) return response()->json(['results' => []]);

    $apiKey = config('services.tmdb.api_key');
    $baseUrl = config('services.tmdb.base_url');

    // Fetch movie results
    $movieResponse = Http::get("{$baseUrl}search/movie", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
        'query' => $query,
    ]);

    // Fetch TV series results
    $tvResponse = Http::get("{$baseUrl}search/tv", [
        'api_key' => $apiKey,
        'language' => 'id-ID',
        'query' => $query,
    ]);

    // Tambahkan URL default Cloudinary
    $defaultPoster = "https://res.cloudinary.com/dj2pofe14/image/upload/images/noimg.png";

    $movies = collect($movieResponse->json()['results'] ?? [])->map(fn($item) => [
        'id' => $item['id'],
        'title' => $item['title'],
        'release_date' => $item['release_date'] ?? '',
        'type' => 'movie',
        'poster_path' => !empty($item['poster_path'])
            ? "https://image.tmdb.org/t/p/w92{$item['poster_path']}"
            : $defaultPoster, // Pakai URL Cloudinary
        'average_rating' => $item['vote_average'] ?? 'N/A',
    ]);

    $tvShows = collect($tvResponse->json()['results'] ?? [])->map(fn($item) => [
        'id' => $item['id'],
        'title' => $item['name'],
        'release_date' => $item['first_air_date'] ?? '',
        'type' => 'tv',
        'poster_path' => !empty($item['poster_path'])
            ? "https://image.tmdb.org/t/p/w92{$item['poster_path']}"
            : $defaultPoster, // Pakai URL Cloudinary
        'average_rating' => $item['vote_average'] ?? 'N/A',
    ]);


    // Gabungkan hasil film dan TV series
    $results = $movies->merge($tvShows)->values();

    return response()->json(['results' => $results]);
});
