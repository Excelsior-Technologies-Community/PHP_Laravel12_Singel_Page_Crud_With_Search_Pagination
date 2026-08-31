<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class RecentlyViewedController extends Controller
{
    /**
     * Display recently viewed items.
     */
    public function index()
    {
        $ids = session()->get('recently_viewed', []);

        if (empty($ids)) {
            $items = collect();
        } else {
            $items = Item::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($item) use ($ids) {
                    return array_search($item->id, $ids);
                })
                ->values();
        }

        return view('recently-viewed.index', compact('items'));
    }

    /**
     * Clear recently viewed items.
     */
    public function clear()
    {
        session()->forget('recently_viewed');

        return redirect()
            ->route('recently-viewed.index')
            ->with('success', 'Recently viewed items cleared successfully.');
    }

    /**
     * Remove one item from recently viewed.
     */
    public function remove($id)
    {
        $recentlyViewed = session()->get('recently_viewed', []);

        $recentlyViewed = array_values(
            array_filter($recentlyViewed, function ($itemId) use ($id) {
                return (int) $itemId !== (int) $id;
            })
        );

        session()->put('recently_viewed', $recentlyViewed);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from recently viewed.',
        ]);
    }
}