<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Watched; // Tambahkan import model Watched
use App\Models\Watchlist; // Tambahkan import model Watched
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();

        // Fetch watched items and order by the latest first
        $watched = Watched::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $watchedCount = $watched->count();

        // Fetch watchlist items and order by the latest first
        $watchlist = Watchlist::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $watchlistCount = $watchlist->count();

        // Fetch favorite items and order by the latest first
        $favorite = Favorite::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $favoriteCount = $favorite->count();


        return view('profile', compact('user', 'watched', 'watchedCount', 'watchlist', 'watchlistCount', 'favorite', 'favoriteCount'));
    }



    public function saveProfile(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'cropped_image' => 'nullable|string',
        ]);

        $user = User::find(Auth::user()->id);

        // Jika user mengupload hasil crop
        if ($request->filled('cropped_image')) {
            $imageData = $request->input('cropped_image');

            // Pastikan ini adalah base64 yang valid
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData)) {
                // Simpan ke Cloudinary
                $uploadedFile = Cloudinary::upload($imageData, [
                    'folder' => 'profile_pictures',
                    'public_id' => 'user_' . $user->id,
                    'overwrite' => true,
                ]);
                // Simpan URL ke kolom 'profile_photo'
                $user->profile_photo = $uploadedFile->getSecurePath();
            } else {
                return back()->withErrors(['profile_photo' => 'Invalid image format.']);
            }
        }

        $user->username = $request->input('username');

        // Simpan perubahan ke database
        $user->save();

        return redirect('/')->with('success', 'Profile updated successfully.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->username = $request->username;

        if ($request->hasFile('profile_photo')) {
            $cloudinaryImage = Cloudinary::upload(
                $request->file('profile_photo')->getRealPath(),
                ['folder' => 'profile_pictures']
            );
            $user->profile_photo = $cloudinaryImage->getSecurePath();
        }

        $user->save();
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }


    public function editProfile()
    {
        $user = Auth::user();
        return view('profile-edit', compact('user'));
    }
}
