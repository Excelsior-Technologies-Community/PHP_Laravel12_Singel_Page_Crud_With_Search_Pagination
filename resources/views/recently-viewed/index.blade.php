<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Recently Viewed Items</title>

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
            background: #f8f9fa;
        }

        .item-card {
            transition: all 0.2s ease;
        }

        .item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.10);
        }

        .item-image {
            width: 100%;
            height: 190px;
            object-fit: cover;
        }

        .empty-icon {
            font-size: 70px;
            color: #adb5bd;
        }

        .price {
            font-weight: 700;
            color: #198754;
        }
    </style>
</head>

<body>

<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                <i class="bi bi-clock-history"></i>
                Recently Viewed
            </h2>

            <p class="text-muted mb-0">
                Your last 10 viewed items
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('items.index') }}"
                class="btn btn-secondary"
            >
                <i class="bi bi-arrow-left"></i>
                Back to Items
            </a>

            @if($items->count() > 0)
                <form
                    action="{{ route('recently-viewed.clear') }}"
                    method="POST"
                    onsubmit="return confirm('Clear all recently viewed items?')"
                >
                    @csrf

                    <button class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Clear All
                    </button>
                </form>
            @endif

        </div>
    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif


    {{-- ITEMS --}}
    @if($items->count() > 0)

        <div class="row g-4">

            @foreach($items as $item)

                <div
                    class="col-md-6 col-lg-4"
                    id="recent-item-{{ $item->id }}"
                >

                    <div class="card item-card h-100">

                        {{-- IMAGE --}}
                        <img
                            src="{{ $item->display_image_url }}"
                            class="card-img-top item-image"
                            alt="{{ $item->name }}"
                        >

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start">

                                <h5 class="card-title mb-2">
                                    {{ $item->name }}
                                </h5>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="removeRecentItem({{ $item->id }})"
                                    title="Remove"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>

                            </div>

                            <p class="text-muted small mb-2">
                                {{ $item->category ?? 'No Category' }}
                            </p>

                            <p class="card-text">
                                {{ \Illuminate\Support\Str::limit($item->description, 100) }}
                            </p>

                            @if($item->price)
                                <div class="price mb-2">
                                    ${{ number_format($item->price, 2) }}
                                </div>
                            @endif

                            <div class="small text-muted mb-3">

                                <span class="me-3">
                                    <i class="bi bi-eye"></i>
                                    {{ $item->views }} views
                                </span>

                                <span>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    {{ number_format($item->average_rating, 1) }}
                                </span>

                            </div>

                            <a
                                href="{{ route('items.index', ['view_id' => $item->id]) }}"
                                class="btn btn-primary w-100"
                            >
                                <i class="bi bi-eye"></i>
                                View Item
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="card">

            <div class="card-body text-center py-5">

                <div class="empty-icon mb-3">
                    <i class="bi bi-clock-history"></i>
                </div>

                <h4>No Recently Viewed Items</h4>

                <p class="text-muted">
                    Items you view will appear here.
                </p>

                <a
                    href="{{ route('items.index') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-box-seam"></i>
                    Browse Items
                </a>

            </div>

        </div>

    @endif

</div>


<script>
function removeRecentItem(id) {

    if (!confirm('Remove this item from recently viewed?')) {
        return;
    }

    fetch(`/recently-viewed/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            const element =
                document.getElementById(`recent-item-${id}`);

            if (element) {
                element.remove();
            }

            if (
                document.querySelectorAll('[id^="recent-item-"]').length === 0
            ) {
                location.reload();
            }
        }
    })
    .catch(error => {
        console.error(error);
        alert('Unable to remove item.');
    });
}
</script>

</body>
</html>