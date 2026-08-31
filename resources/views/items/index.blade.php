<!DOCTYPE html>
<html>
<head>
    <title>Laravel CRUD - Advanced Features</title>
    @php use Illuminate\Support\Str; @endphp
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        body.dark-mode {
            background-color: #1a1a2e;
            color: #e0e0e0;
        }
        body.dark-mode .card, body.dark-mode .table, body.dark-mode .modal-content, body.dark-mode .form-control, body.dark-mode .form-select {
            background-color: #16213e;
            color: #e0e0e0;
            border-color: #0f3460;
        }
        body.dark-mode .table-dark { --bs-table-bg: #0f3460; }
        body.dark-mode .bg-light { background-color: #1a1a2e !important; }
        body.dark-mode .text-dark { color: #e0e0e0 !important; }
        body.dark-mode .btn-secondary { background-color: #0f3460; border-color: #0f3460; }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: #212529;
            color: white;
            padding-top: 60px;
            z-index: 1000;
            transition: transform 0.3s;
        }
        .sidebar.collapsed { transform: translateX(-100%); }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #0d6efd;
            color: white;
        }
        .sidebar .sidebar-header {
            padding: 0 20px 10px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .main-content.expanded { margin-left: 0; }
        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        .image-gallery { display: flex; flex-wrap: wrap; gap: 8px; }
        .image-gallery img { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer; }
        .star { color: #ffc107; cursor: pointer; font-size: 1.2rem; }
        .star.empty { color: #dee2e6; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .filter-card { margin-bottom: 15px; }
        .bulk-actions { display: none; margin-bottom: 15px; }
        .item-card { transition: 0.2s; }
        .item-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toastContainer"></div>

{{-- SIDEBAR TOGGLE --}}
<button class="btn btn-dark sidebar-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
</button>

{{-- DARK MODE TOGGLE --}}
<button class="btn btn-outline-dark position-fixed" style="top:15px; right:15px; z-index:1001;" onclick="toggleDarkMode()">
    <i class="bi bi-moon" id="darkModeIcon"></i>
</button>

{{-- SIDEBAR --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">Menu</div>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.index') && !request()->trash ? 'active' : '' }}">
        <i class="bi bi-house"></i> Items
    </a>
    <a href="{{ route('items.trash') }}" class="{{ request()->routeIs('items.trash') ? 'active' : '' }}">
        <i class="bi bi-trash"></i> Trash
    </a>
    <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.index') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> Activity Logs
    </a>
    <a href="{{ route('favorites.index') }}" class="{{ request()->routeIs('favorites.index') ? 'active' : '' }}">
        <i class="bi bi-star"></i> My Favorites
    </a>
    <a href="{{ route('recently-viewed.index') }}" class="{{ request()->routeIs('recently-viewed.index') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> Recently Viewed
    </a>
</div>

{{-- MAIN CONTENT --}}
<div class="main-content">
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Items Management</h3>
            <div>
                <a href="{{ route('items.index', ['mode' => 'create']) }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Create Item
                </a>
            </div>
        </div>

        {{-- TOAST --}}
        <div class="toast-container" id="liveToastContainer"></div>

        {{-- CREATE / EDIT FORM (AJAX) --}}
        @if($mode === 'create' || $editItem)
        <div class="card mb-4 filter-card" id="formCard">
            <div class="card-body">
                <h5 class="mb-3">{{ $editItem ? 'Edit Item' : 'Add New Item' }}</h5>
                <form id="itemForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="{{ $editItem ? 'PUT' : 'POST' }}">
                    <input type="hidden" name="edit_id" value="{{ $editItem->id ?? '' }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" name="name" class="form-control" id="nameInput"
                                   value="{{ $editItem->name ?? '' }}" required>
                            <div class="text-danger" id="nameError"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Category</label>
                            <select name="category" class="form-select" id="categoryInput">
                                <option value="">Select Category</option>
                                <option value="Electronics" {{ ($editItem->category ?? '') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="Clothing" {{ ($editItem->category ?? '') == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="Books" {{ ($editItem->category ?? '') == 'Books' ? 'selected' : '' }}>Books</option>
                                <option value="Home" {{ ($editItem->category ?? '') == 'Home' ? 'selected' : '' }}>Home</option>
                                <option value="Sports" {{ ($editItem->category ?? '') == 'Sports' ? 'selected' : '' }}>Sports</option>
                                <option value="Other" {{ ($editItem->category ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select" id="statusInput">
                                <option value="active" {{ ($editItem->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($editItem->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" id="priceInput"
                                   value="{{ $editItem->price ?? '' }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" id="descriptionInput" rows="3">{{ $editItem->description ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control" id="imageInput" accept="image/*" onchange="previewImage(event)">
                            @if($editItem && $editItem->image)
                                <div class="mt-2">
                                    <label>Current Image:</label><br>
                                    <img src="{{ $editItem->display_image_url }}" id="currentImage" width="100">
                                </div>
                            @endif
                            <img id="imagePreview" style="display:none; margin-top:10px;" width="120">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Gallery Images</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*" onchange="previewGallery(event)">
                            <div class="image-gallery mt-2" id="galleryPreview"></div>
                            @if($editItem && $editItem->images)
                                <div class="image-gallery mt-2">
                                    @foreach($editItem->images as $img)
                                        <div style="position:relative;">
                                            <img src="{{ $editItem->resolveImageUrl($img) }}" width="80">
                                            <button type="button" class="btn btn-sm btn-danger" style="position:absolute; top:-5px; right:-5px; padding:2px 5px;" onclick="deleteGalleryImage('{{ $img }}', {{ $editItem->id }})">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        {{ $editItem ? 'Update' : 'Save' }}
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
        @endif

        {{-- FILTERS --}}
        <div class="card mb-4 filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('items.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest)</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (Low)</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price (High)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                        </div>
                        <div class="col-md-1">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary w-100 mt-1"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- IMPORT / EXPORT --}}
        <div class="d-flex gap-2 mb-3">
            <form action="{{ route('items.import-csv') }}" method="POST" enctype="multipart/form-data" class="d-inline" id="importForm">
                @csrf
                <input type="file" name="csv_file" accept=".csv" class="d-none" id="csvFileInput" onchange="document.getElementById('importForm').submit()">
                <button type="button" class="btn btn-success" onclick="document.getElementById('csvFileInput').click()">
                    <i class="bi bi-upload"></i> Import CSV
                </button>
            </form>
            <a href="{{ route('items.export-csv') }}" class="btn btn-info">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>

        {{-- BULK ACTIONS --}}
        <div class="bulk-actions card p-3" id="bulkActions">
            <span class="me-3" id="selectedCount">0 selected</span>
            <button class="btn btn-danger" onclick="bulkDelete()">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
            <button class="btn btn-secondary ms-2" onclick="clearSelection()">Cancel</button>
        </div>

        {{-- ITEMS TABLE --}}
        <div class="card">
            <div class="card-body">
                <form id="bulkForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Rating</th>
                                    <th>Views</th>
                                    <th width="280">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr class="item-card">
                                    <td><input type="checkbox" class="item-checkbox" value="{{ $item->id }}" onchange="updateBulkActions()"></td>
                                    <td>
                                        <img src="{{ $item->display_image_url }}" width="60" height="45" style="cursor:pointer; object-fit:cover; border-radius:4px;" onclick="showItem({{ $item->id }})" alt="{{ $item->name }}">
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                    </td>
                                    <td>{{ $item->category ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->price ? '$' . number_format($item->price, 2) : 'N/A' }}</td>
                                    <td>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($item->average_rating) ? '-fill' : '' }} star{{ $i > $item->average_rating ? ' empty' : '' }}"></i>
                                        @endfor
                                        <small>({{ $item->rating_count }})</small>
                                    </td>
                                    <td>{{ $item->views }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showItem({{ $item->id }})" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning favorite-btn" data-item-id="{{ $item->id }}" onclick="toggleFavorite({{ $item->id }})" title="Favorite">
                                            <i class="bi bi-star{{ $item->is_favorite ? '-fill' : '' }}"></i>
                                        </button>
                                        <a href="{{ route('items.index', ['edit_id' => $item->id]) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="duplicateItem({{ $item->id }})" title="Duplicate">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteItem({{ $item->id }}, '{{ $item->name }}')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ITEM DETAIL MODAL --}}
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="itemModalBody">
                Loading...
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

let selectedItems = [];
let toastTimeout;

// Dark Mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark ? '1' : '0');
    document.getElementById('darkModeIcon').className = isDark ? 'bi bi-sun' : 'bi bi-moon';
}
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
        document.getElementById('darkModeIcon').className = 'bi bi-sun';
    }
});

// Sidebar
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
}

// Toast
function showToast(message, type = 'success') {
    const container = document.getElementById('liveToastContainer');
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// Image Preview
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

function previewGallery(event) {
    const container = document.getElementById('galleryPreview');
    container.innerHTML = '';
    Array.from(event.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

// AJAX Form Submit
document.getElementById('itemForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const defaultSubmitText = '{{ $editItem ? "Update" : "Save" }}';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
    document.querySelectorAll('[id$="Error"]').forEach(el => el.textContent = '');

    $.ajax({
        url: '{{ $editItem ? route("items.update", $editItem->id) : route("items.store") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-HTTP-Method-Override': document.getElementById('formMethod').value
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: function(xhr) {
            let message = xhr.responseJSON?.message || 'Error saving item';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                let errors = xhr.responseJSON.errors;
                message = Object.values(errors).flat()[0] || message;
                for (let key in errors) {
                    const errorEl = document.getElementById(key + 'Error');
                    if (errorEl) {
                        errorEl.textContent = errors[key][0];
                    }
                }
            }
            showToast(message, 'danger');
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = defaultSubmitText;
        }
    });
});

// Show Item in Modal
function showItem(id) {
    $.get(`/show/${id}`, function(data) {
        if (data.success) {
            const item = data.item;
            let galleryHtml = '';
            if (item.gallery_image_urls && item.gallery_image_urls.length > 0) {
                galleryHtml = '<h6>Gallery:</h6><div class="image-gallery">' +
                    item.gallery_image_urls.map(img => `<img src="${img}" onclick="window.open('${img}', '_blank')">`).join('') +
                    '</div>';
            }
            document.getElementById('itemModalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="${item.display_image_url}" class="img-fluid mb-3">
                        ${galleryHtml}
                    </div>
                    <div class="col-md-6">
                        <h4>${item.name}</h4>
                        <p><strong>Category:</strong> ${item.category || 'N/A'}</p>
                        <p><strong>Status:</strong> ${item.status}</p>
                        <p><strong>Price:</strong> ${item.price ? '$' + item.price : 'N/A'}</p>
                        <p><strong>Views:</strong> ${item.views}</p>
                        <p><strong>Rating:</strong> <span id="modalAverageRating">${item.average_rating}</span> / 5 (<span id="modalRatingCount">${item.rating_count}</span> reviews)</p>
                        <p><strong>Description:</strong><br>${item.description || 'No description'}</p>
                        <p><strong>Created:</strong> ${item.created_at}</p>

                        {{-- Comments Section --}}
                        <hr>
                        <h5>Comments & Ratings</h5>
                        <form onsubmit="addComment(event, ${item.id})">
                            <div class="mb-2">
                                <textarea name="comment" class="form-control" placeholder="Write a comment..." required></textarea>
                            </div>
                            <div class="mb-2">
                                <label>Rating:</label>
                                <select name="rating" class="form-select" style="width:100px;">
                                    <option value="">None</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
                        </form>
                        <div id="commentsList-${item.id}" class="mt-3"></div>
                    </div>
                </div>
            `;
            loadComments(item.id);
            new bootstrap.Modal(document.getElementById('itemModal')).show();
        }
    });
}

function loadComments(itemId) {
    $.get(`/comments/${itemId}`, function(data) {
        if (data.success) {
            let html = '';
            data.comments.data.forEach(c => {
                const rating = c.rating || 0;
                html += `<div class="border p-2 mb-2 rounded">
                    <strong>${c.user?.name || 'Guest'}</strong>
                    <span class="text-warning">${'★'.repeat(rating)}${'☆'.repeat(5 - rating)}</span>
                    <p class="mb-0">${c.comment}</p>
                    <small class="text-muted">${c.created_at}</small>
                </div>`;
            });
            document.getElementById(`commentsList-${itemId}`).innerHTML = html;
        }
    });
}

function addComment(event, itemId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    $.ajax({
        url: `/comments/${itemId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                document.getElementById('modalAverageRating').textContent = response.average_rating;
                document.getElementById('modalRatingCount').textContent = response.rating_count;
                loadComments(itemId);
                form.reset();
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            const message = errors ? Object.values(errors).flat()[0] : (xhr.responseJSON?.message || 'Comment save failed');
            showToast(message, 'danger');
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Add Comment';
        }
    });
}


// Favorite
function toggleFavorite(id) {

    const button = document.querySelector(
        `.favorite-btn[data-item-id="${id}"]`
    );

    const icon = button ? button.querySelector('i') : null;

    // Prevent double clicks
    if (button) {
        button.disabled = true;
    }

    $.ajax({
        url: `/favorite/${id}`,
        type: 'POST',

        data: {
            _token: '{{ csrf_token() }}'
        },

        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },

        success: function(response) {

            if (response.success) {

                // Update star icon
                if (icon) {
                    if (response.is_favorite) {
                        icon.className = 'bi bi-star-fill';
                    } else {
                        icon.className = 'bi bi-star';
                    }
                }

                // Update button title
                if (button) {
                    button.title = response.is_favorite
                        ? 'Remove from favorites'
                        : 'Add to favorites';
                }

                showToast(response.message, 'success');

            } else {

                showToast(
                    response.message || 'Favorite update failed',
                    'danger'
                );
            }
        },

        error: function(xhr) {

            console.error('Favorite Error:', xhr.responseText);

            let message = 'Favorite update failed';

            if (xhr.responseJSON) {
                message = xhr.responseJSON.message || message;
            }

            showToast(message, 'danger');
        },

        complete: function() {

            if (button) {
                button.disabled = false;
            }
        }
    });
}



// Delete Item
function deleteItem(id, name) {
    if (confirm(`Delete "${name}"?`)) {
        $.ajax({
            url: `/delete/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                }
            }
        });
    }
}

// Duplicate Item
function duplicateItem(id) {
    $.post(`/duplicate/${id}`, { _token: '{{ csrf_token() }}' }, function(data) {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 500);
        }
    });
}

// Delete Gallery Image
function deleteGalleryImage(image, itemId) {
    $.ajax({
        url: `/image/${itemId}/${image.replace('image/gallery/', '')}`,
        type: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 500);
            }
        }
    });
}

// Bulk Delete
function toggleSelectAll(source) {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const checked = document.querySelectorAll('.item-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    document.getElementById('selectedCount').textContent = checked.length + ' selected';
    bulkActions.style.display = checked.length > 0 ? 'block' : 'none';
    selectedItems = Array.from(checked).map(cb => cb.value);
}

function bulkDelete() {
    if (selectedItems.length === 0) return;
    if (!confirm(`Delete ${selectedItems.length} items?`)) return;

    $.ajax({
        url: '/bulk-delete',
        type: 'POST',
        data: { ids: selectedItems, _token: '{{ csrf_token() }}' },
        success: function(data) {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 500);
            }
        }
    });
}

function clearSelection() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

</script>
</body>
</html>
