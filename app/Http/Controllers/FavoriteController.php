<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($itemId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to use favorites']);
        }

        $item = Item::findOrFail($itemId);
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)->where('item_id', $itemId)->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Removed from favorites';
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'item_id' => $itemId,
            ]);
            $message = 'Added to favorites';
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite,
            'favorites_count' => $item->favorites()->count(),
        ]);
    }

    public function myFavorites()
    {
        if (!Auth::check()) {
            $favoriteIds = session('favorite_items', []);
            $favorites = Item::whereIn('id', $favoriteIds)->latest()->paginate(10);

            return view('favorites.index', compact('favorites'));
        }

        $favorites = Favorite::where('user_id', Auth::id())
            ->with('item')
            ->latest()
            ->paginate(10);

        return view('favorites.index', compact('favorites'));
    }
}
