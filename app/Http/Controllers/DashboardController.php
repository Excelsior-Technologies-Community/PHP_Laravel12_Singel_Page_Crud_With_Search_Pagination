<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display item statistics dashboard.
     */
    public function index()
    {
        // ---------------------------------------------------------
        // BASIC ITEM STATISTICS
        // ---------------------------------------------------------

        $totalItems = Item::count();

        $activeItems = Item::where('status', 'active')->count();

        $inactiveItems = Item::where('status', 'inactive')->count();

        $totalViews = Item::sum('views');

        // ---------------------------------------------------------
        // CATEGORY STATISTICS
        // ---------------------------------------------------------

        $categoryStats = Item::select(
                'category',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ---------------------------------------------------------
        // MOST VIEWED ITEMS
        // ---------------------------------------------------------

        $mostViewedItems = Item::orderByDesc('views')
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // HIGHEST RATED ITEMS
        // ---------------------------------------------------------
        // average_rating and rating_count are calculated values,
        // not actual columns in the items table.
        //
        // We use subqueries instead of GROUP BY so that MySQL's
        // ONLY_FULL_GROUP_BY mode does not cause an error.

        $highestRatedItems = Item::query()
            ->select('items.*')
            ->selectSub(function ($query) {
                $query->from('comments')
                    ->selectRaw('COALESCE(AVG(rating), 0)')
                    ->whereColumn('comments.item_id', 'items.id')
                    ->whereNotNull('rating');
            }, 'average_rating')
            ->selectSub(function ($query) {
                $query->from('comments')
                    ->selectRaw('COUNT(rating)')
                    ->whereColumn('comments.item_id', 'items.id')
                    ->whereNotNull('rating');
            }, 'rating_count')
            ->orderByDesc('average_rating')
            ->orderByDesc('rating_count')
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // RECENT ITEMS
        // ---------------------------------------------------------

        $recentItems = Item::latest()
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // TOTAL COMMENTS
        // ---------------------------------------------------------

        $totalComments = 0;

        if (DB::getSchemaBuilder()->hasTable('comments')) {
            $totalComments = DB::table('comments')->count();
        }

        // ---------------------------------------------------------
        // TOTAL FAVORITES
        // ---------------------------------------------------------

        $totalFavorites = 0;

        if (DB::getSchemaBuilder()->hasTable('favorites')) {
            $totalFavorites = DB::table('favorites')->count();
        }

        // ---------------------------------------------------------
        // TOP FAVORITED ITEMS
        // ---------------------------------------------------------

        $mostFavoritedItems = collect();

        if (DB::getSchemaBuilder()->hasTable('favorites')) {

            $mostFavoritedItems = Item::query()
                ->select('items.*')
                ->selectSub(function ($query) {
                    $query->from('favorites')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('favorites.item_id', 'items.id');
                }, 'favorites_count')
                ->orderByDesc('favorites_count')
                ->take(5)
                ->get();
        }

        // ---------------------------------------------------------
        // OVERALL AVERAGE RATING
        // ---------------------------------------------------------

        $averageRating = 0;

        if (DB::getSchemaBuilder()->hasTable('comments')) {

            $averageRating = DB::table('comments')
                ->whereNotNull('rating')
                ->avg('rating');

            $averageRating = round($averageRating ?? 0, 2);
        }

        // ---------------------------------------------------------
        // RETURN DASHBOARD
        // ---------------------------------------------------------

        return view('dashboard.index', compact(
            'totalItems',
            'activeItems',
            'inactiveItems',
            'totalViews',
            'totalComments',
            'totalFavorites',
            'averageRating',
            'categoryStats',
            'mostViewedItems',
            'highestRatedItems',
            'mostFavoritedItems',
            'recentItems'
        ));
    }
}

