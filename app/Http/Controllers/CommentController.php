<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Comment added!',
            'comment' => $comment->load('user'),
            'average_rating' => $item->average_rating,
            'rating_count' => $item->rating_count,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $comment->update($request->only('comment', 'rating'));

        return response()->json(['success' => true, 'message' => 'Comment updated!', 'comment' => $comment->load('user')]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted!']);
    }

    public function itemComments($itemId)
    {
        $item = Item::findOrFail($itemId);
        $comments = $item->comments()->with('user')->latest()->paginate(10);

        return response()->json(['success' => true, 'comments' => $comments]);
    }
}
