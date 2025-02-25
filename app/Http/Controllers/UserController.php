<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id); // Cari pengguna berdasarkan ID
    
        $watched = $user->watchedMovies()->orderBy('created_at', 'desc')->get();
        $watchlist = $user->watchlistMovies()->orderBy('created_at', 'desc')->get();
        $favorite = $user->favoriteMovies()->orderBy('created_at', 'desc')->get();
    
        $watchedCount = $watched->count();
        $watchlistCount = $watchlist->count();
        $favoriteCount = $favorite->count();
    
        return view('userdetail', compact('user', 'watched', 'watchlist', 'favorite', 'watchedCount', 'watchlistCount', 'favoriteCount'));
    }
}
