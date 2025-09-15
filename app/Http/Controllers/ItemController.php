<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\ItemStoreRequest; // ← 追加

class ItemController extends Controller
{
    /**
     * 商品一覧
     * - recommend: 自分の出品を除外（ログイン時のみ）
     * - mylist   : いいね済み（likes）のみ
     * - q        : 商品名の部分一致
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'recommend');
        $q   = trim((string) $request->get('q', ''));

        if ($tab === 'mylist' && auth()->check()) {
            // マイリスト（ユーザーが「いいね」した商品）
            $query = auth()->user()->likedItems()->latest();
        } else {
            // おすすめ：ログイン中は自分の出品を除外、ゲストは全件
            $query = Item::query()->latest();
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }
        }

        if ($q !== '') {
            $query->where('items.name', 'like', "%{$q}%");
        }

        $items = $query->paginate(12)->appends(['tab' => $tab, 'q' => $q]);

        return view('items.index', compact('items', 'tab', 'q'));
    }

    /** 出品フォーム */
    public function create()
    {
        return view('items.create', [
            'categories' => Category::all(),
            'conditions' => Condition::all(),
        ]);
    }

    /**
     * 出品保存
     * - バリデーションは ItemStoreRequest で実施
     * - カテゴリ複数（pivot）
     * - 旧スキーマ互換で items.category_id が残る場合の先頭カテゴリ補完
     */
    public function store(ItemStoreRequest $request)
    {
        // 検証済みデータ
        $validated = $request->validated();

        // 画像保存（public ディスク）
        $path = $request->file('image')->store('items', 'public');

        // 新規レコード（フォーム brand → DB brand_name）
        $data = [
            'user_id'      => auth()->id(),
            'image_path'   => $path,
            'condition_id' => $validated['condition_id'],
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'price'        => $validated['price'],
            'status'       => 1,
            'brand_name'   => $validated['brand'] ?? null,
        ];

        // 旧スキーマ互換：items.category_id が残っていれば先頭カテゴリを入れる
        if (Schema::hasColumn('items', 'category_id')) {
            $firstCategoryId = $validated['category_ids'][0] ?? null;
            if (!is_null($firstCategoryId)) {
                $data['category_id'] = $firstCategoryId;
            }
        }

        $item = Item::create($data);

        // 中間テーブル（item_category）へカテゴリ紐付け
        $item->categories()->sync($validated['category_ids']);

        return redirect()
            ->route('items.index', ['tab' => 'mylist'])
            ->with('success', '出品が完了しました。');
    }

    /** 商品詳細 */
    public function show(Item $item)
    {
        // 必要な関連と件数をまとめて取得（コメント→ユーザー→プロフィールまで）
        $item->load([
            'user.profile',
            'condition',
            'categories',
            'comments.user.profile',
        ])->loadCount([
            'likedBy as likes_count',
            'comments',
        ]);

        $liked = auth()->check() ? auth()->user()->hasLiked($item) : false;

        // 類似商品（同カテゴリ・自分の出品除外）
        $similar = Item::query()
            ->where('id', '!=', $item->id)
            ->when(auth()->check(), fn ($q) => $q->where('user_id', '!=', auth()->id()))
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $item->categories->pluck('id')))
            ->latest()->take(8)->get();

        // 出品者の他商品
        $sellerItems = Item::query()
            ->where('user_id', $item->user_id)
            ->where('id', '!=', $item->id)
            ->latest()->take(8)->get();

        return view('items.show', compact('item', 'liked', 'similar', 'sellerItems'));
    }
}
