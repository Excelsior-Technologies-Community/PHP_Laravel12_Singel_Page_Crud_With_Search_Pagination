<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        $period = $request->get('period', 'all');

        $allowedPeriods = [
            'all',
            'today',
            '7days',
            '30days',
        ];

        if (!in_array($period, $allowedPeriods)) {
            $period = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | BASIC ITEM STATISTICS
        |--------------------------------------------------------------------------
        */

        $itemQuery = Item::query();

        if ($period === 'today') {

            $itemQuery->whereDate(
                'created_at',
                today()
            );

        } elseif ($period === '7days') {

            $itemQuery->where(
                'created_at',
                '>=',
                now()->subDays(7)
            );

        } elseif ($period === '30days') {

            $itemQuery->where(
                'created_at',
                '>=',
                now()->subDays(30)
            );
        }

        $totalItems = (clone $itemQuery)->count();

        $activeItems = (clone $itemQuery)
            ->where('status', 'active')
            ->count();

        $inactiveItems = (clone $itemQuery)
            ->where('status', 'inactive')
            ->count();

        $totalViews = (clone $itemQuery)
            ->sum('views');


        /*
        |--------------------------------------------------------------------------
        | CATEGORY STATISTICS
        |--------------------------------------------------------------------------
        */

        $categoryQuery = Item::query();

        if ($period === 'today') {

            $categoryQuery->whereDate(
                'created_at',
                today()
            );

        } elseif ($period === '7days') {

            $categoryQuery->where(
                'created_at',
                '>=',
                now()->subDays(7)
            );

        } elseif ($period === '30days') {

            $categoryQuery->where(
                'created_at',
                '>=',
                now()->subDays(30)
            );
        }

        $categoryStats = $categoryQuery
            ->select(
                'category',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | MOST VIEWED ITEMS
        |--------------------------------------------------------------------------
        */

        $mostViewedItems = (clone $itemQuery)
            ->orderByDesc('views')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | HIGHEST RATED ITEMS
        |--------------------------------------------------------------------------
        */

        $highestRatedItems = Item::query()
            ->select('items.*')

            ->selectSub(function ($query) {

                $query
                    ->from('comments')
                    ->selectRaw(
                        'COALESCE(AVG(rating), 0)'
                    )
                    ->whereColumn(
                        'comments.item_id',
                        'items.id'
                    )
                    ->whereNotNull('rating')
                    ->where('status', 'approved');

            }, 'average_rating')

            ->selectSub(function ($query) {

                $query
                    ->from('comments')
                    ->selectRaw('COUNT(rating)')
                    ->whereColumn(
                        'comments.item_id',
                        'items.id'
                    )
                    ->whereNotNull('rating')
                    ->where('status', 'approved');

            }, 'rating_count')

            ->orderByDesc('average_rating')
            ->orderByDesc('rating_count')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RECENT ITEMS
        |--------------------------------------------------------------------------
        */

        $recentItems = Item::latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | COMMENT STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalComments = 0;
        $approvedComments = 0;
        $pendingComments = 0;
        $averageRating = 0;

        if (DB::getSchemaBuilder()->hasTable('comments')) {

            $commentQuery = DB::table('comments');

            if ($period === 'today') {

                $commentQuery->whereDate(
                    'created_at',
                    today()
                );

            } elseif ($period === '7days') {

                $commentQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(7)
                );

            } elseif ($period === '30days') {

                $commentQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                );
            }

            $totalComments = (clone $commentQuery)->count();

            $approvedComments = (clone $commentQuery)
                ->where('status', 'approved')
                ->count();

            $pendingComments = (clone $commentQuery)
                ->where('status', 'pending')
                ->count();

            $averageRating = (clone $commentQuery)
                ->where('status', 'approved')
                ->whereNotNull('rating')
                ->avg('rating');

            $averageRating = round(
                $averageRating ?? 0,
                2
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FAVORITES
        |--------------------------------------------------------------------------
        */

        $totalFavorites = 0;

        $mostFavoritedItems = collect();

        if (DB::getSchemaBuilder()->hasTable('favorites')) {

            $favoriteQuery = DB::table('favorites');

            if ($period === 'today') {

                $favoriteQuery->whereDate(
                    'created_at',
                    today()
                );

            } elseif ($period === '7days') {

                $favoriteQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(7)
                );

            } elseif ($period === '30days') {

                $favoriteQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                );
            }

            $totalFavorites = $favoriteQuery->count();


            $mostFavoritedItems = Item::query()
                ->select('items.*')

                ->selectSub(function ($query) use ($period) {

                    $query
                        ->from('favorites')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn(
                            'favorites.item_id',
                            'items.id'
                        );

                    if ($period === 'today') {

                        $query->whereDate(
                            'created_at',
                            today()
                        );

                    } elseif ($period === '7days') {

                        $query->where(
                            'created_at',
                            '>=',
                            now()->subDays(7)
                        );

                    } elseif ($period === '30days') {

                        $query->where(
                            'created_at',
                            '>=',
                            now()->subDays(30)
                        );
                    }

                }, 'favorites_count')

                ->orderByDesc('favorites_count')
                ->take(5)
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalActivities = 0;
        $todayActivities = 0;

        if (DB::getSchemaBuilder()->hasTable('activity_logs')) {

            $activityQuery = DB::table('activity_logs');

            if ($period === 'today') {

                $activityQuery->whereDate(
                    'created_at',
                    today()
                );

            } elseif ($period === '7days') {

                $activityQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(7)
                );

            } elseif ($period === '30days') {

                $activityQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                );
            }

            $totalActivities = $activityQuery->count();

            $todayActivities = DB::table('activity_logs')
                ->whereDate(
                    'created_at',
                    today()
                )
                ->count();
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', compact(

            'period',

            'totalItems',
            'activeItems',
            'inactiveItems',
            'totalViews',

            'totalComments',
            'approvedComments',
            'pendingComments',
            'averageRating',

            'totalFavorites',

            'totalActivities',
            'todayActivities',

            'categoryStats',

            'mostViewedItems',
            'highestRatedItems',
            'mostFavoritedItems',
            'recentItems'
        ));
    }
}