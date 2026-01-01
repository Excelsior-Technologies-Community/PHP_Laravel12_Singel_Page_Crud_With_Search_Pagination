# PHP_Laravel12_Singel_Page_Crud_With_Search_Pagination

# Step 1 : Install Laravel 12 and Create new project

```php
composer create-project laravel/laravel PHP_Laravel12_Singel_Page_Crud_With_Search_Pagination
```
# Step 2 : Setup Database for.env file
```php
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=your database name
 DB_USERNAME=root
 DB_PASSWORD=
```
# Now Create Single Page Crud With Searching and pagination: 
# Step 3 : Create Migration For Database Table
```php
php  artisan make:migration create_items_table
```
Migration File
database/migrations/xxxx_xx_xx_create_items_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table ->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
```
# Run Migration
```php
php artisan migrate
```

# Step 4 : Create Model For Item
```php
php artisan make:model Item
```
Model File
app/Models/Item.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
protected $fillable = [
    'name',
    'description',
    'image',
];
}
```
# Step 5 : Create Controller 
```php
php artisan make:controller ItemController
```
Controller File
app/Http/Controllers/ItemController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
class ItemController extends Controller
{
public function index(Request $request)
{
    $search = $request->search;

    $items = Item::when($search, function ($query, $search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
        })
        ->latest()
        ->paginate(3)
        ->withQueryString();

    $editItem = null;
    if ($request->has('edit_id')) {
        $editItem = Item::find($request->edit_id);
    }

    $mode = $request->mode; // 👈 create | null

    return view('items.index', compact('items', 'editItem', 'search', 'mode'));
}
 public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
        ]);

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('image'), $imageName);
        }

        Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()->route('items.index');
    }

    // UPDATE
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
        ]);

        $imageName = $item->image;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('image'), $imageName);
        }

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()->route('items.index');
    }

    // DELETE
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index');
    }
}
```
# Step 6 : Create Route for routes/web.php
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::post('/store', [ItemController::class, 'store'])->name('items.store');
Route::post('/update/{item}', [ItemController::class, 'update'])->name('items.update');
Route::get('/delete/{item}', [ItemController::class, 'destroy'])->name('items.delete');
```

# Step 7 : Now Create Blade file For resource/view/items folder:
# resources/views/items/index.blade.php
```php
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
```


# Step 8 : Last Run Server and paste this url
```php
php artisan serve
http://127.0.0.1:8000/
```
 <img width="1652" height="759" alt="image" src="https://github.com/user-attachments/assets/f96f8d76-099a-4f4b-944e-e6272146aee2" />


# Now Click Create Item and Show Create Form  For this Current index page .
 <img width="1410" height="835" alt="image" src="https://github.com/user-attachments/assets/c2130d44-17b4-4cca-8fff-321c3ebabb68" />

 <img width="1369" height="909" alt="image" src="https://github.com/user-attachments/assets/aa9dd471-682c-46a5-b659-0265af99ffd6" />











