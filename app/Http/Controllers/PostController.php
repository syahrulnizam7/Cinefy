<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'likes', 'images'])->latest()->get();
        return view('posts', compact('posts'));
    }

    public function store(Request $request)
{
    $request->validate([
        'content' => 'required|string|max:280',
        'images.*' => 'nullable',
    ]);

    // 1. Buat Post baru
    $post = Post::create([
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    // 2. Jika kiriman adalah file (dari posts.blade)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $cloudinaryImage = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'posts'
            ]);
            PostImage::create([
                'post_id' => $post->id,
                'image_path' => $cloudinaryImage->getSecurePath(),
            ]);
        }
    }

    // 3. Jika kiriman adalah JSON (dari detail.blade)
    if ($request->filled('images')) {
        foreach ($request->input('images') as $imageData) {
            if (is_string($imageData) && $json = json_decode($imageData, true)) {
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => json_encode($json),
                ]);
            }
        }
    }

    return redirect('/posts')->with('success', 'Post berhasil dibuat!');
}


    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return back()->with('error', 'Kamu tidak memiliki izin untuk menghapus postingan ini.');
        }

        foreach ($post->images as $image) {
            Cloudinary::destroy($image->image_path);
            $image->delete();
        }

        $post->delete();

        return back()->with('success', 'Post berhasil dihapus.');
    }
}
