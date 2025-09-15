<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /** 購入画面 */
    public function show(Item $item)
    {
        // 自分の出品は買えない／売切れチェック
        if ((int)$item->user_id === (int)auth()->id()) {
            return redirect()->route('items.show', $item)
                ->with('error', '自分の商品は購入できません。');
        }
        if ((int)($item->status ?? 1) === 0) {
            return redirect()->route('items.show', $item)
                ->with('error', 'この商品は売り切れです。');
        }

        // ユーザーの配送先（購入画面で表示用）
        $user = auth()->user()->load('profile');

        // デザイン側で使う想定値
        $quantity       = 1;
        $shippingFee    = 0;
        $subtotal       = (int)$item->price * $quantity;
        $total          = $subtotal + $shippingFee;
        $paymentOptions = [
            'convenience_store' => 'コンビニ払い',
            'credit_card'       => 'クレジットカード',
            'bank_transfer'     => '銀行振込',
        ];

        return view('purchase.show', [
            'item'           => $item,
            'profile'        => $user->profile,
            'quantity'       => $quantity,
            'shippingFee'    => $shippingFee,
            'subtotal'       => $subtotal,
            'total'          => $total,
            'paymentOptions' => $paymentOptions,
        ]);
    }

    /** 購入確定 */
    public function store(Request $request, Item $item)
    {
        // 入力は数量と支払い方法だけ（住所はプロフィールから取得）
        $validated = $request->validate([
            'quantity'        => ['required', 'integer', 'min:1', 'max:10'],
            'payment_method'  => ['required', 'in:convenience_store,credit_card,bank_transfer'],
        ]);

        // プロフィールに住所がない場合は差し戻し
        $profile = auth()->user()->load('profile')->profile;
        if (!$profile || empty($profile->postal_code) || empty($profile->address)) {
            return back()
                ->withErrors(['address' => '配送先が未設定です。「変更する」から住所を登録してください。'])
                ->withInput();
        }

        // 郵便番号を 123-4567 に正規化
        $postal = $this->normalizePostal($profile->postal_code);

        try {
            DB::transaction(function () use ($item, $validated, $profile, $postal) {
                // 行ロックで二重購入防止
                $fresh = Item::lockForUpdate()->findOrFail($item->id);

                // 自分の出品・売切れガード（直前で状態が変わった場合に備える）
                if ((int)$fresh->user_id === (int)auth()->id()) {
                    abort(403, '自分の商品は購入できません。');
                }
                if ((int)($fresh->status ?? 1) === 0) {
                    abort(409, '売り切れです。');
                }

                $qty       = (int)$validated['quantity'];
                $unitPrice = (int)$fresh->price;
                $shipping  = 0;
                $total     = $unitPrice * $qty + $shipping;

                Purchase::create([
                    'user_id'        => auth()->id(),
                    'item_id'        => $fresh->id,
                    'quantity'       => $qty,
                    'price'          => $unitPrice,
                    'shipping_fee'   => $shipping,
                    'total'          => $total,
                    'postal_code'    => $postal,
                    'address'        => $profile->address,
                    'building_name'  => $profile->building_name,
                    'payment_method' => $validated['payment_method'],
                ]);

                // 売切れに更新
                $fresh->status = 0;
                $fresh->save();
            });
        } catch (\Throwable $e) {
            // 行ロック中の競合や abort() を拾ってメッセージ化
            $msg = $e->getCode() === 409 ? 'この商品は売り切れです。' : ($e->getMessage() ?: '購入に失敗しました。');
            return back()->with('error', $msg);
        }

        return redirect()->route('items.show', $item)
            ->with('success', '購入が完了しました！');
    }

    /** 郵便番号を 123-4567 形式に整形 */
    private function normalizePostal(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);
        return strlen($digits) === 7
            ? substr($digits, 0, 3) . '-' . substr($digits, 3)
            : $value;
    }
}
