<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Dashboard</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet"
>

<style>
    body {
        background: #f5f7fb;
        font-family: Arial, sans-serif;
    }

    .sidebar {
        min-height: 100vh;
        background: #111827;
        color: white;
        padding: 25px 15px;
    }

    .sidebar-title {
        font-size: 22px;
        font-weight: 700;
        padding: 10px 15px 25px;
    }

    .sidebar a {
        display: block;
        color: #cbd5e1;
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 5px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #2563eb;
        color: white;
    }

    .main {
        padding: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid #e5e7eb;
        height: 100%;
        transition: .2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .07);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 22px;
    }

    .stat-number {
        font-size: 30px;
        font-weight: 700;
        margin-top: 12px;
    }

    .stat-label {
        color: #64748b;
        font-size: 14px;
    }

    .panel {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px;
        height: 100%;
    }

    .panel-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 13px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .item-row:last-child {
        border-bottom: 0;
    }

    .badge-soft {
        background: #eff6ff;
        color: #2563eb;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .progress {
        height: 8px;
        border-radius: 20px;
    }

    .filter-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 25px;
    }

    .filter-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 5px;
        font-weight: 600;
    }

    @media(max-width: 768px) {
        .sidebar {
            min-height: auto;
        }

        .main {
            padding: 18px;
        }
    }
</style>


</head>

<body>

<div class="container-fluid">

<div class="row">

    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}

    <div class="col-lg-2 sidebar">

        <div class="sidebar-title">
            <i class="bi bi-grid"></i>
            Admin Panel
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="active"
        >
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
        </a>

        <a href="{{ route('items.index') }}">
            <i class="bi bi-box me-2"></i>
            Items
        </a>

        <a href="{{ route('favorites.index') }}">
            <i class="bi bi-star me-2"></i>
            Favorites
        </a>

        <a href="{{ route('recently-viewed.index') }}">
            <i class="bi bi-clock-history me-2"></i>
            Recently Viewed
        </a>

        <a href="{{ route('activity-logs.index') }}">
            <i class="bi bi-activity me-2"></i>
            Activity Logs
        </a>

    </div>


    {{-- =====================================================
         MAIN CONTENT
    ====================================================== --}}

    <div class="col-lg-10 main">

        {{-- HEADER --}}

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

            <div>

                <h2 class="fw-bold mb-1">
                    Dashboard
                </h2>

                <p class="text-muted mb-0">
                    Overview of your application
                </p>

            </div>

            {{-- DATE FILTER --}}

            <form
                method="GET"
                action="{{ route('dashboard') }}"
            >

                <label class="filter-label">
                    Dashboard Period
                </label>

                <select
                    name="period"
                    class="form-select"
                    onchange="this.form.submit()"
                >

                    <option
                        value="all"
                        {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}
                    >
                        All Time
                    </option>

                    <option
                        value="today"
                        {{ ($period ?? '') === 'today' ? 'selected' : '' }}
                    >
                        Today
                    </option>

                    <option
                        value="7days"
                        {{ ($period ?? '') === '7days' ? 'selected' : '' }}
                    >
                        Last 7 Days
                    </option>

                    <option
                        value="30days"
                        {{ ($period ?? '') === '30days' ? 'selected' : '' }}
                    >
                        Last 30 Days
                    </option>

                </select>

            </form>

        </div>


        {{-- =====================================================
             ACTIVE FILTER DISPLAY
        ====================================================== --}}

        @if(($period ?? 'all') !== 'all')

            <div class="alert alert-primary border-0 shadow-sm">

                <i class="bi bi-calendar3 me-2"></i>

                Showing dashboard statistics for:

                <strong>
                    @if($period === 'today')
                        Today
                    @elseif($period === '7days')
                        Last 7 Days
                    @elseif($period === '30days')
                        Last 30 Days
                    @endif
                </strong>

                <a
                    href="{{ route('dashboard') }}"
                    class="float-end text-decoration-none"
                >
                    Reset
                </a>

            </div>

        @endif


        {{-- =====================================================
             FIRST STATISTICS ROW
        ====================================================== --}}

        <div class="row g-4 mb-4">

            {{-- TOTAL ITEMS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-box"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($totalItems ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Total Items
                    </div>

                </div>

            </div>


            {{-- ACTIVE ITEMS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($activeItems ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Active Items
                    </div>

                </div>

            </div>


            {{-- TOTAL VIEWS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-eye"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($totalViews ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Total Views
                    </div>

                </div>

            </div>


            {{-- FAVORITES --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-star"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($totalFavorites ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Total Favorites
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             SECOND STATISTICS ROW
        ====================================================== --}}

        <div class="row g-4 mb-4">

            {{-- COMMENTS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-chat"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($totalComments ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Total Comments
                    </div>

                </div>

            </div>


            {{-- PENDING COMMENTS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($pendingComments ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Pending Comments
                    </div>

                </div>

            </div>


            {{-- APPROVED COMMENTS --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($approvedComments ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Approved Comments
                    </div>

                </div>

            </div>


            {{-- AVERAGE RATING --}}

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($averageRating ?? 0, 2) }}
                    </div>

                    <div class="stat-label">
                        Average Rating
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ACTIVITY STATISTICS
        ====================================================== --}}

        <div class="row g-4 mb-4">

            {{-- TOTAL ACTIVITIES --}}

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($totalActivities ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Total Activities
                    </div>

                </div>

            </div>


            {{-- TODAY ACTIVITIES --}}

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($todayActivities ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Today's Activities
                    </div>

                </div>

            </div>


            {{-- TODAY COMMENTS --}}

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>

                    <div class="stat-number">
                        {{ number_format($todayComments ?? 0) }}
                    </div>

                    <div class="stat-label">
                        Today's Comments
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             CATEGORY + MOST VIEWED
        ====================================================== --}}

        <div class="row g-4 mb-4">

            {{-- CATEGORY --}}

            <div class="col-lg-6">

                <div class="panel">

                    <div class="panel-title">
                        <i class="bi bi-tags me-2"></i>
                        Items by Category
                    </div>

                    @forelse($categoryStats as $category)

                        <div class="item-row">

                            <strong>
                                {{ $category->category }}
                            </strong>

                            <span class="badge-soft">
                                {{ $category->total }}
                            </span>

                        </div>

                    @empty

                        <p class="text-muted mb-0">
                            No category data available.
                        </p>

                    @endforelse

                </div>

            </div>


            {{-- MOST VIEWED --}}

            <div class="col-lg-6">

                <div class="panel">

                    <div class="panel-title">
                        <i class="bi bi-fire me-2"></i>
                        Most Viewed Items
                    </div>

                    @forelse($mostViewedItems as $item)

                        <div class="item-row">

                            <div>

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small text-muted">
                                    {{ $item->category }}
                                </div>

                            </div>

                            <span class="badge-soft">

                                <i class="bi bi-eye me-1"></i>

                                {{ number_format($item->views) }}

                            </span>

                        </div>

                    @empty

                        <p class="text-muted mb-0">
                            No items available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- =====================================================
             RATING + FAVORITES
        ====================================================== --}}

        <div class="row g-4 mb-4">

            {{-- HIGHEST RATED --}}

            <div class="col-lg-6">

                <div class="panel">

                    <div class="panel-title">
                        <i class="bi bi-trophy me-2"></i>
                        Highest Rated Items
                    </div>

                    @forelse($highestRatedItems as $item)

                        <div class="item-row">

                            <div>

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small text-muted">

                                    {{ $item->rating_count }}

                                    ratings

                                </div>

                            </div>

                            <span class="badge bg-warning text-dark">

                                <i class="bi bi-star-fill"></i>

                                {{ number_format($item->average_rating, 1) }}

                            </span>

                        </div>

                    @empty

                        <p class="text-muted mb-0">
                            No ratings available.
                        </p>

                    @endforelse

                </div>

            </div>


            {{-- MOST FAVORITED --}}

            <div class="col-lg-6">

                <div class="panel">

                    <div class="panel-title">
                        <i class="bi bi-heart me-2"></i>
                        Most Favorited Items
                    </div>

                    @forelse($mostFavoritedItems as $item)

                        <div class="item-row">

                            <div>

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small text-muted">
                                    {{ $item->category }}
                                </div>

                            </div>

                            <span class="badge-soft">

                                <i class="bi bi-heart-fill"></i>

                                {{ $item->favorites_count }}

                            </span>

                        </div>

                    @empty

                        <p class="text-muted mb-0">
                            No favorites available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- =====================================================
             RECENT ITEMS + QUICK SUMMARY
        ====================================================== --}}

        <div class="row g-4">

            {{-- RECENT ITEMS --}}

            <div class="col-lg-8">

                <div class="panel">

                    <div class="panel-title">
                        <i class="bi bi-clock me-2"></i>
                        Recent Items
                    </div>

                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                            <tr>

                                <th>Item</th>

                                <th>Category</th>

                                <th>Status</th>

                                <th>Created</th>

                            </tr>

                            </thead>

                            <tbody>

                            @forelse($recentItems as $item)

                                <tr>

                                    <td>
                                        <strong>
                                            {{ $item->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $item->category ?: '-' }}
                                    </td>

                                    <td>

                                        @if($item->status === 'active')

                                            <span class="badge bg-success">
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                {{ ucfirst($item->status) }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>
                                        {{ optional($item->created_at)->format('d M Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center text-muted"
                                    >
                                        No recent items found.
                                    </td>

                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- QUICK SUMMARY --}}

            <div class="col-lg-4">

                <div class="panel">

                    <div class="panel-title">
                        Quick Summary
                    </div>


                    {{-- ACTIVE ITEMS --}}

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Active Items
                            </span>

                            <strong>
                                {{ $activeItems ?? 0 }}
                            </strong>

                        </div>

                        @php
                            $activePercentage = ($totalItems ?? 0) > 0
                                ? (($activeItems ?? 0) / $totalItems) * 100
                                : 0;
                        @endphp

                        <div class="progress">

                            <div
                                class="progress-bar"
                                style="width: {{ min($activePercentage, 100) }}%"
                            ></div>

                        </div>

                    </div>


                    {{-- APPROVED COMMENTS --}}

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Approved Comments
                            </span>

                            <strong>
                                {{ $approvedComments ?? 0 }}
                            </strong>

                        </div>

                        @php
                            $commentPercentage = ($totalComments ?? 0) > 0
                                ? (($approvedComments ?? 0) / $totalComments) * 100
                                : 0;
                        @endphp

                        <div class="progress">

                            <div
                                class="progress-bar"
                                style="width: {{ min($commentPercentage, 100) }}%"
                            ></div>

                        </div>

                    </div>


                    {{-- PENDING COMMENTS --}}

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Pending Comments
                            </span>

                            <strong>
                                {{ $pendingComments ?? 0 }}
                            </strong>

                        </div>

                        @php
                            $pendingPercentage = ($totalComments ?? 0) > 0
                                ? (($pendingComments ?? 0) / $totalComments) * 100
                                : 0;
                        @endphp

                        <div class="progress">

                            <div
                                class="progress-bar bg-warning"
                                style="width: {{ min($pendingPercentage, 100) }}%"
                            ></div>

                        </div>

                    </div>


                    {{-- TODAY ACTIVITY --}}

                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Today's Activities
                            </span>

                            <strong>
                                {{ $todayActivities ?? 0 }}
                            </strong>

                        </div>

                    </div>


                    {{-- TODAY COMMENTS --}}

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>
                                Today's Comments
                            </span>

                            <strong>
                                {{ $todayComments ?? 0 }}
                            </strong>

                        </div>

                    </div>


                    <a
                        href="{{ route('activity-logs.index') }}"
                        class="btn btn-primary w-100"
                    >

                        <i class="bi bi-activity me-1"></i>

                        View Activity Logs

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


</div>

</body>

</html>
