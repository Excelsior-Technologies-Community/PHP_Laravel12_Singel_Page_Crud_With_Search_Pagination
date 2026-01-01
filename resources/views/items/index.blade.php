<!DOCTYPE html>
<html>
<head>
    <title>Single Page CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Items</h3>

        {{-- CREATE BUTTON --}}
        <a href="{{ route('items.index', ['mode' => 'create']) }}"
           class="btn btn-success">
            + Create Item
        </a>
    </div>

    {{-- ========================= --}}
    {{-- CREATE / EDIT FORM --}}
    {{-- ========================= --}}
    @if($mode === 'create' || $editItem)

    <div class="card mb-4">
        <div class="card-body">

            <h5 class="mb-3">
                {{ $editItem ? 'Edit Item' : 'Add Item' }}
            </h5>

            <form method="POST"
                  action="{{ $editItem ? route('items.update', $editItem->id) : route('items.store') }}"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $editItem->name ?? '' }}">
                </div>

                <div class="mb-2">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $editItem->description ?? '' }}</textarea>
                </div>

                <div class="mb-2">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control"
                           onchange="previewImage(event)">
                </div>

                {{-- OLD IMAGE --}}
                @if($editItem && $editItem->image)
                    <div class="mb-2">
                        <label>Old Image</label><br>
                        <img src="{{ asset('image/'.$editItem->image) }}" width="80">
                    </div>
                @endif

                {{-- NEW PREVIEW --}}
                <div class="mb-2">
                    <label>New Image Preview</label><br>
                    <img id="imagePreview" style="display:none;" width="120">
                </div>

                <button class="btn btn-primary">
                    {{ $editItem ? 'Update' : 'Save' }}
                </button>

                <a href="{{ route('items.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>

        </div>
    </div>
    @endif

    {{-- ========================= --}}
    {{-- SEARCH --}}
    {{-- ========================= --}}
    <form method="get" action="{{ route('items.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       class="form-control"
                       placeholder="Search name or description">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary">Search</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    {{-- ========================= --}}
    {{-- LIST --}}
    {{-- ========================= --}}
    <table class="table table-bordered">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th width="150">Action</th>
        </tr>

        @foreach($items as $item)
        <tr>
            <td>
                @if($item->image)
                    <img src="{{ asset('image/'.$item->image) }}" width="60">
                @endif
            </td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td>
                <a href="{{ route('items.index', ['edit_id' => $item->id]) }}"
                   class="btn btn-sm btn-warning">Edit</a>

                <a href="{{ route('items.delete', $item->id) }}"
                   onclick="return confirm('Delete?')"
                   class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        @endforeach
    </table>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>

</div>

{{-- IMAGE PREVIEW --}}
<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
</script>

</body>
</html>
