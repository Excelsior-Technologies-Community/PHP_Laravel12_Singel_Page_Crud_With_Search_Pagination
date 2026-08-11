<!DOCTYPE html>
<html>
<head>
    <title>My Favorites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>My Favorites</h3>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to Items</a>
    </div>

    @if($favorites->count() > 0)
    <table class="table table-bordered">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price</th>
        </tr>
        @foreach($favorites as $fav)
        @php $item = $fav instanceof \App\Models\Item ? $fav : $fav->item; @endphp
        <tr>
            <td>
                <img src="{{ $item->display_image_url }}" width="60" height="45" style="object-fit:cover; border-radius:4px;" alt="{{ $item->name }}">
            </td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->category ?? 'N/A' }}</td>
            <td>{{ $item->price ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </table>

    <div class="mt-3">
        {{ $favorites->links('pagination::bootstrap-5') }}
    </div>
    @else
    <p class="text-center text-muted">No favorites yet!</p>
    @endif
</div>
</body>
</html>
