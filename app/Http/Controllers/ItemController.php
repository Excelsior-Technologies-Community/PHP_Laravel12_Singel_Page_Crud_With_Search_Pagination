<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // Advanced search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }

        $items = $query->paginate(10)->withQueryString();

        // Categories for filter
        $categories = Item::distinct()->pluck('category')->filter()->values();

        // Edit item
        $editItem = null;
        if ($request->has('edit_id')) {
            $editItem = Item::find($request->edit_id);
        }

        $mode = $request->mode;

        return view('items.index', compact('items', 'editItem', 'categories', 'mode'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'category', 'status', 'price']);

        // Handle single image
        if ($request->hasFile('image')) {
            $this->ensureImageDirectories();
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('image'), $imageName);
            $data['image'] = 'image/' . $imageName;
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            $this->ensureImageDirectories();
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('image/gallery'), $imageName);
                $images[] = 'image/gallery/' . $imageName;
            }
            $data['images'] = $images;
        }

        $item = Item::create($data);

        ActivityLog::log('created', $item, "Created item: {$item->name}", null, $item->toArray());

        return response()->json(['success' => true, 'message' => 'Item created successfully!', 'item' => $item]);
    }

    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $oldValues = $item->toArray();

        $data = $request->only(['name', 'description', 'category', 'status', 'price']);

        // Handle single image
        if ($request->hasFile('image')) {
            $this->ensureImageDirectories();
            $oldImagePath = $this->localImagePath($item->image);
            if ($oldImagePath && file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('image'), $imageName);
            $data['image'] = 'image/' . $imageName;
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            $this->ensureImageDirectories();
            $images = $item->images ?? [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('image/gallery'), $imageName);
                $images[] = 'image/gallery/' . $imageName;
            }
            $data['images'] = $images;
        }

        $item->update($data);
        $newValues = $item->fresh()->toArray();

        ActivityLog::log('updated', $item, "Updated item: {$item->name}", $oldValues, $newValues);

        return response()->json(['success' => true, 'message' => 'Item updated successfully!', 'item' => $item]);
    }

    public function destroy(Item $item)
    {
        $itemName = $item->name;
        $oldValues = $item->toArray();
        $item->delete();

        ActivityLog::log('deleted', $item, "Soft deleted item: {$itemName}", $oldValues, null);

        return response()->json(['success' => true, 'message' => 'Item deleted successfully!']);
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid items selected!']);
        }

        $items = Item::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($items as $item) {
            $oldValues = $item->toArray();
            $item->delete();
            ActivityLog::log('bulk_deleted', $item, "Bulk deleted item: {$item->name}", $oldValues, null);
            $count++;
        }

        return response()->json(['success' => true, 'message' => "{$count} items deleted successfully!"]);
    }

    public function restore($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $item->restore();

        ActivityLog::log('restored', $item, "Restored item: {$item->name}", null, $item->toArray());

        return response()->json(['success' => true, 'message' => 'Item restored successfully!']);
    }

    public function forceDelete($id)
    {
        $item = Item::onlyTrashed()->findOrFail($id);
        $itemName = $item->name;

        $imagePath = $this->localImagePath($item->image);
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }

        if ($item->images) {
            foreach ($item->images as $image) {
                $galleryPath = $this->localImagePath($image);
                if ($galleryPath && file_exists($galleryPath)) {
                    unlink($galleryPath);
                }
            }
        }

        $item->forceDelete();

        ActivityLog::log('force_deleted', null, "Permanently deleted item: {$itemName}", null, null);

        return response()->json(['success' => true, 'message' => 'Item permanently deleted!']);
    }

    public function duplicate($id)
    {
        $item = Item::findOrFail($id);
        $newItem = $item->replicate();
        $newItem->name = $item->name . ' (Copy)';
        $newItem->image = null;
        $newItem->images = null;
        $newItem->views = 0;
        $newItem->save();

        ActivityLog::log('duplicated', $newItem, "Duplicated item from: {$item->name}", null, $newItem->toArray());

        return response()->json(['success' => true, 'message' => 'Item duplicated successfully!', 'item' => $newItem]);
    }

    public function importCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid file!']);
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            Item::create([
                'name' => $data['name'] ?? 'Untitled',
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? null,
                'status' => $data['status'] ?? 'active',
                'price' => $data['price'] ?? 0,
            ]);
            $count++;
        }

        fclose($handle);

        ActivityLog::log('imported', null, "Imported {$count} items from CSV", null, null);

        return response()->json(['success' => true, 'message' => "{$count} items imported successfully!"]);
    }

    public function exportCsv()
    {
        $items = Item::whereNull('deleted_at')->get();

        $csv = "Name,Description,Category,Status,Price,Created At\n";

        foreach ($items as $item) {
            $csv .= "\"{$item->name}\",\"{$item->description}\",\"{$item->category}\",\"{$item->status}\",{$item->price},\"{$item->created_at}\"\n";
        }

        $fileName = 'items_' . date('Y-m-d_H-i-s') . '.csv';

        ActivityLog::log('exported', null, "Exported {$items->count()} items to CSV", null, null);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function show($id)
    {
        $item = Item::with(['comments.user', 'favorites'])->findOrFail($id);
        $item->increment('views');

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function toggleFavorite($id)
    {
        $item = Item::findOrFail($id);

        if (!Auth::check()) {
            $favorites = session('favorite_items', []);

            if (in_array($id, $favorites)) {
                $favorites = array_values(array_diff($favorites, [$id]));
                $isFavorite = false;
                $message = 'Removed from favorites';
            } else {
                $favorites[] = $id;
                $favorites = array_values(array_unique($favorites));
                $isFavorite = true;
                $message = 'Added to favorites';
            }

            session(['favorite_items' => $favorites]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite,
                'favorites_count' => $item->favorites()->count(),
            ]);
        }

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)->where('item_id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
            $message = 'Removed from favorites';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'item_id' => $id,
            ]);
            $isFavorite = true;
            $message = 'Added to favorites';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite,
            'favorites_count' => $item->favorites()->count(),
        ]);
    }

    public function deleteImage($id, $image)
    {
        $item = Item::findOrFail($id);
        $imagePath = $image;

        if (!str_starts_with($imagePath, 'image/')) {
            $imagePath = 'image/gallery/' . $imagePath;
        }

        if ($item->image == $imagePath) {
            $item->image = null;
        }

        if ($item->images) {
            $images = array_filter($item->images, function ($img) use ($imagePath) {
                return $img != $imagePath;
            });
            $item->images = array_values($images);
        }

        $localPath = $this->localImagePath($imagePath);
        if ($localPath && file_exists($localPath)) {
            unlink($localPath);
        }

        $item->save();

        return response()->json(['success' => true, 'message' => 'Image deleted!']);
    }

    public function trash()
    {
        $items = Item::onlyTrashed()->latest()->paginate(10);

        return view('items.trash', compact('items'));
    }

    private function localImagePath(?string $image): ?string
    {
        if (!$image || str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return null;
        }

        return public_path(str_starts_with($image, 'image/') ? $image : 'image/' . $image);
    }

    private function ensureImageDirectories(): void
    {
        foreach ([public_path('image'), public_path('image/gallery')] as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }
        }
    }
}
