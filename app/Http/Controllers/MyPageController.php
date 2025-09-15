<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;

class MyPageController extends Controller
{
    /**
     * マイページ
     * ?tab=listed | purchased（default: listed）
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tab  = $request->query('tab', 'listed'); // 出品した商品がデフォルト

        if ($tab === 'purchased') {
            // 購入した商品の Item 一覧
            $itemIds = Purchase::where('user_id', $user->id)->pluck('item_id');
            $items   = Item::whereIn('id', $itemIds)->latest()->paginate(12);
        } else {
            // 出品した商品
            $items = Item::where('user_id', $user->id)->latest()->paginate(12);
            $tab   = 'listed';
        }

        return view('mypage.index', compact('user', 'items', 'tab'));
    }
}
