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
