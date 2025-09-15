<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Item; // 購入画面へ戻すときの存在確認に使用（不要なら削除OK）

class AddressController extends Controller
{
    public function edit(Request $request): View
    {
        $user = auth()->user();
        $profile = $user->profile()->firstOrCreate([]);

        // 購入画面から来た場合に保持（?item=ID）
        $itemId = $request->query('item');

        return view('address.edit', compact('profile', 'itemId'));
    }

    public function update(UpdateAddressRequest $request): \Illuminate\Http\RedirectResponse
{
    $user = auth()->user();

    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        $request->validated()
    );

    // hidden の item があれば購入ページへ戻す
    if ($request->filled('item')) {
        return redirect()
            ->route('purchase.show', ['item' => $request->input('item')])
            ->with('status', '配送先を更新しました。');
    }

    // 単独アクセス時は編集画面に留まる（お好みでマイページ等に変更可）
    return redirect()
        ->route('address.edit')
        ->with('status', '配送先を更新しました。');
}

}
