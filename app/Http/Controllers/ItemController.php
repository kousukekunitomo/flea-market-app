<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item; 
use App\Models\Category;
use App\Models\Condition;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'recommend'); // デフォルトはおすすめ

        if ($tab === 'mylist') {
            // 「マイリスト」はログインユーザーの商品
            $items = Item::where('user_id', auth()->id())
                        ->latest()
                        ->paginate(9);
        } else {
            // 「おすすめ」は全商品
            $items = Item::latest()->paginate(9);
        }

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image',
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
        ]);

        $path = $request->file('image')->store('items', 'public');

        Item::create([
            'user_id' => auth()->id(),
            'image_path' => $path,
            'category_id' => $validated['category_id'],
            'condition_id' => $validated['condition_id'],
            'name' => $validated['name'],
            'brand' => $validated['brand'],
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('items.index', ['tab' => 'mylist'])
                         ->with('success', '出品が完了しました');
    }
}
