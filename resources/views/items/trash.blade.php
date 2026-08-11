<!DOCTYPE html>
<html>
<head>
    <title>Trash - Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Trash</h3>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to Items</a>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Deleted At</th>
            <th width="200">Action</th>
        </tr>
        @foreach($items as $item)
        <tr>
            <td>
                @if($item->image)
                    <img src="{{ asset($item->image) }}" width="60">
                @endif
            </td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->deleted_at->format('Y-m-d H:i') }}</td>
            <td>
                <form action="{{ route('items.restore', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('POST')
                    <button class="btn btn-sm btn-success" onclick="return confirm('Restore?')">Restore</button>
                </form>
                <form action="{{ route('items.force-delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete Forever</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="mt-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>
</body>
</html>
