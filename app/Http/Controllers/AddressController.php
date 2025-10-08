<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
    /** /items/{item}/address/edit */
    public function edit(Request $request, Item $item): View
    {
        $user = auth()->user();
        $profile = $user->profile()->firstOrCreate([]);

        // ビュー側で購入画面に戻すために item を渡す（hiddenで使う）
        return view('address.edit', [
            'profile' => $profile,
            'item'    => $item,
        ]);
    }

    /** /items/{item}/address */
    public function update(UpdateAddressRequest $request, Item $item): RedirectResponse
    {
        $user = auth()->user();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->validated()
        );

        // 住所更新後は購入ページへ戻す
        return redirect()
            ->route('purchase.show', $item)
            ->with('status', '配送先を更新しました。');
    }
}
