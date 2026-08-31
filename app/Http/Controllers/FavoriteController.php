<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite for an item.
     *
     * Logged-in users:
     *     Favorites are stored in the favorites table.
     *
     * Guests:
     *     Favorites are stored in the session.
     */
    public function toggle(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        /*
        |--------------------------------------------------------------------------
        | Logged-in user
        |--------------------------------------------------------------------------
        */
        if (Auth::check()) {
            $userId = Auth::id();

            $favorite = Favorite::where('user_id', $userId)
                ->where('item_id', $itemId)
                ->first();

            if ($favorite) {
                $favorite->delete();

                $isFavorite = false;
                $message = 'Removed from favorites';
            } else {
                Favorite::create([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                ]);

                $isFavorite = true;
                $message = 'Added to favorites';
            }

            $favoritesCount = Favorite::where('item_id', $itemId)->count();

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite,
                'favorites_count' => $favoritesCount,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Guest user
        |--------------------------------------------------------------------------
        */
        $favoriteIds = $request->session()->get('favorite_items', []);

        // Make sure IDs are integers
        $favoriteIds = array_map('intval', $favoriteIds);

        if (in_array((int) $itemId, $favoriteIds, true)) {

            // Remove item from session favorites
            $favoriteIds = array_values(
                array_diff($favoriteIds, [(int) $itemId])
            );

            $isFavorite = false;
            $message = 'Removed from favorites';

        } else {

            // Add item to session favorites
            $favoriteIds[] = (int) $itemId;

            $isFavorite = true;
            $message = 'Added to favorites';
        }

        $request->session()->put('favorite_items', $favoriteIds);

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite,
            'favorites_count' => count($favoriteIds),
        ]);
    }

    /**
     * Display user's favorites.
     */
    public function myFavorites(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Logged-in user
        |--------------------------------------------------------------------------
        */
        if (Auth::check()) {

            $favorites = Favorite::where('user_id', Auth::id())
                ->with('item')
                ->latest()
                ->paginate(10);

            return view('favorites.index', compact('favorites'));
        }

        /*
        |--------------------------------------------------------------------------
        | Guest user
        |--------------------------------------------------------------------------
        */
        $favoriteIds = $request->session()->get('favorite_items', []);

        $favoriteIds = array_map('intval', $favoriteIds);

        $favorites = Item::whereIn('id', $favoriteIds)
            ->latest()
            ->paginate(10);

        return view('favorites.index', compact('favorites'));
    }
}

