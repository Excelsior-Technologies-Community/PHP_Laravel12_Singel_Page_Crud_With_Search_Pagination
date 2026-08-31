<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Item Statistics Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            transition: all 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .chart-bar {
            height: 12px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
        }

        .chart-bar-fill {
            height: 100%;
            border-radius: 10px;
            background: #0d6efd;
        }

        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .section-card {
            border: none;
            border-radius: 15px;
        }

    </style>

</head>

<body>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                <i class="bi bi-speedometer2"></i>
                Item Statistics
            </h2>

            <p class="text-muted mb-0">
                Overview of your item management system
            </p>

        </div>

        <div>

            <a
                href="{{ route('items.index') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-box-seam"></i>
                Manage Items
            </a>

        </div>

    </div>


    {{-- STATISTICS --}}
    <div class="row g-4 mb-4">

        {{-- TOTAL ITEMS --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted">
                                Total Items
                            </div>

                            <div class="stat-number">
                                {{ number_format($totalItems) }}
                            </div>

                        </div>

                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ACTIVE --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted">
                                Active Items
                            </div>

                            <div class="stat-number text-success">
                                {{ number_format($activeItems) }}
                            </div>

                        </div>

                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- VIEWS --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted">
                                Total Views
                            </div>

                            <div class="stat-number text-info">
                                {{ number_format($totalViews) }}
                            </div>

                        </div>

                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="bi bi-eye"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- FAVORITES --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted">
                                Total Favorites
                            </div>

                            <div class="stat-number text-warning">
                                {{ number_format($totalFavorites) }}
                            </div>

                        </div>

                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bi bi-star-fill"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SECOND ROW --}}
    <div class="row g-4 mb-4">

        {{-- INACTIVE --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card">

                <div class="card-body">

                    <div class="text-muted">
                        Inactive Items
                    </div>

                    <div class="stat-number text-secondary">
                        {{ number_format($inactiveItems) }}
                    </div>

                </div>

            </div>

        </div>


        {{-- COMMENTS --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card">

                <div class="card-body">

                    <div class="text-muted">
                        Total Comments
                    </div>

                    <div class="stat-number text-danger">
                        {{ number_format($totalComments) }}
                    </div>

                </div>

            </div>

        </div>


        {{-- RATING --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card">

                <div class="card-body">

                    <div class="text-muted">
                        Average Rating
                    </div>

                    <div class="stat-number text-warning">
                        {{ number_format($averageRating, 2) }}
                        <small>/ 5</small>
                    </div>

                </div>

            </div>

        </div>


        {{-- RECENTLY VIEWED --}}
        <div class="col-md-6 col-xl-3">

            <div class="card dashboard-card">

                <div class="card-body">

                    <div class="text-muted">
                        Recently Viewed
                    </div>

                    <div class="stat-number text-primary">
                        {{ count(session('recently_viewed', [])) }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- CATEGORY + MOST VIEWED --}}
    <div class="row g-4 mb-4">

        {{-- CATEGORY STATISTICS --}}
        <div class="col-lg-6">

            <div class="card section-card">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-tags"></i>
                        Items by Category
                    </h5>

                    @if($categoryStats->count() > 0)

                        @php
                            $maxCategory =
                                $categoryStats->max('total') ?: 1;
                        @endphp

                        @foreach($categoryStats as $category)

                            <div class="mb-3">

                                <div class="d-flex justify-content-between mb-1">

                                    <span>
                                        {{ $category->category }}
                                    </span>

                                    <strong>
                                        {{ $category->total }}
                                    </strong>

                                </div>

                                <div class="chart-bar">

                                    <div
                                        class="chart-bar-fill"
                                        style="
                                            width:
                                            {{ ($category->total / $maxCategory) * 100 }}%;
                                        "
                                    ></div>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <p class="text-muted">
                            No category data available.
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- MOST VIEWED --}}
        <div class="col-lg-6">

            <div class="card section-card">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-fire"></i>
                        Most Viewed Items
                    </h5>

                    @foreach($mostViewedItems as $item)

                        <div class="d-flex align-items-center mb-3">

                            <img
                                src="{{ $item->display_image_url }}"
                                class="item-image me-3"
                                alt="{{ $item->name }}"
                            >

                            <div class="flex-grow-1">

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small text-muted">
                                    {{ $item->category ?? 'N/A' }}
                                </div>

                            </div>

                            <span class="badge bg-info">
                                <i class="bi bi-eye"></i>
                                {{ number_format($item->views) }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>


    {{-- THIRD ROW --}}
    <div class="row g-4 mb-4">

        {{-- HIGHEST RATED --}}
        <div class="col-lg-6">

            <div class="card section-card">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-star-fill text-warning"></i>
                        Highest Rated Items
                    </h5>

                    @forelse($highestRatedItems as $item)

                        <div class="d-flex align-items-center mb-3">

                            <img
                                src="{{ $item->display_image_url }}"
                                class="item-image me-3"
                                alt="{{ $item->name }}"
                            >

                            <div class="flex-grow-1">

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small">

                                    <span class="text-warning">
                                        ★
                                    </span>

                                    {{ number_format($item->average_rating, 1) }}

                                    <span class="text-muted">
                                        ({{ $item->rating_count }} reviews)
                                    </span>

                                </div>

                            </div>

                        </div>

                    @empty

                        <p class="text-muted">
                            No ratings available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- MOST FAVORITED --}}
        <div class="col-lg-6">

            <div class="card section-card">

                <div class="card-body">

                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-heart-fill text-danger"></i>
                        Most Favorited Items
                    </h5>

                    @forelse($mostFavoritedItems as $item)

                        <div class="d-flex align-items-center mb-3">

                            <img
                                src="{{ $item->display_image_url }}"
                                class="item-image me-3"
                                alt="{{ $item->name }}"
                            >

                            <div class="flex-grow-1">

                                <strong>
                                    {{ $item->name }}
                                </strong>

                                <div class="small text-muted">
                                    {{ $item->category ?? 'N/A' }}
                                </div>

                            </div>

                            <span class="badge bg-danger">

                                <i class="bi bi-star-fill"></i>

                                {{ $item->favorites_count }}

                            </span>

                        </div>

                    @empty

                        <p class="text-muted">
                            No favorite data available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- RECENT ITEMS --}}
    <div class="card section-card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock"></i>
                    Recently Added Items
                </h5>

                <a
                    href="{{ route('items.index') }}"
                    class="btn btn-sm btn-outline-primary"
                >
                    View All
                </a>

            </div>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Item</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Views</th>
                            <th>Created</th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($recentItems as $item)

                        <tr>

                            <td>

                                <div class="d-flex align-items-center">

                                    <img
                                        src="{{ $item->display_image_url }}"
                                        class="item-image me-3"
                                        alt="{{ $item->name }}"
                                    >

                                    <strong>
                                        {{ $item->name }}
                                    </strong>

                                </div>

                            </td>

                            <td>
                                {{ $item->category ?? 'N/A' }}
                            </td>

                            <td>

                                <span
                                    class="badge bg-{{
                                        $item->status === 'active'
                                            ? 'success'
                                            : 'secondary'
                                    }}"
                                >
                                    {{ ucfirst($item->status) }}
                                </span>

                            </td>

                            <td>

                                @if($item->price)
                                    ${{ number_format($item->price, 2) }}
                                @else
                                    N/A
                                @endif

                            </td>

                            <td>
                                {{ number_format($item->views) }}
                            </td>

                            <td>
                                {{ $item->created_at->format('d M Y') }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>