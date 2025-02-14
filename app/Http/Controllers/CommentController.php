<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back();
    }
    public function update(Request $request, $id)
    {
        $request->validate(['content' => 'required']);

        $comment = Comment::findOrFail($id);

        // Pastikan hanya pemilik komentar yang bisa mengedit
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->update(['content' => $request->content]);

        return back();
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Pastikan hanya pemilik komentar yang bisa menghapus
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back();
    }
}
