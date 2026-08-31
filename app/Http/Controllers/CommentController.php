<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store comment.
     */
    public function store(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => 'pending',
        ]);

        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Comment submitted for approval!',
            'comment' => $comment->load('user'),
            'average_rating' => $item->average_rating,
            'rating_count' => $item->rating_count,
        ]);
    }


    /**
     * Update comment.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->update([
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated!',
            'comment' => $comment->load('user'),
        ]);
    }


    /**
     * Delete comment.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted!',
        ]);
    }


    /**
     * Get item comments.
     */
    public function itemComments($itemId)
    {
        $item = Item::findOrFail($itemId);

        $comments = $item
            ->comments()
            ->where('status', 'approved')
            ->with('user')
            ->oldest()
            ->paginate(5);

        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);
    }


    /**
     * Approve comment.
     */
    public function approve(Comment $comment)
    {
        $comment->update([
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment approved successfully!',
        ]);
    }
}