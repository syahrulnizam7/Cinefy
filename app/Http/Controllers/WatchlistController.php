<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller {
    public function store(Request $request) {
        $user = Auth::user();
    
        $watchlist = Watchlist::firstOrCreate(
            [
                'user_id' => $user->id,
                'tmdb_id' => $request->tmdb_id,
            ],
            [
                'title' => $request->title,
                'poster_path' => $request->poster_path,
                'type' => $request->type,
                'vote_average' => $request->vote_average,
                'release_date' => $request->release_date,
            ]
        );
    
        return response()->json([
            'message' => 'Added to watchlist successfully',
            'watchlist' => $watchlist,
        ], 201);
    }
    

    public function index(Request $request) {
        $user = Auth::user();
        $exists = Watchlist::where('user_id', $user->id)->where('tmdb_id', $request->tmdb_id)->exists();

        return response()->json(['watchlist' => $exists]);
    }

    public function destroy(Request $request) {
        $user = Auth::user();
        $watchlist = Watchlist::where('user_id', $user->id)->where('tmdb_id', $request->tmdb_id)->first();

        if ($watchlist) {
            $watchlist->delete();
            return response()->json(['success' => true, 'message' => 'Removed from watchlist']);
        }

        return response()->json(['success' => false, 'message' => 'Item not found']);
    }
}
