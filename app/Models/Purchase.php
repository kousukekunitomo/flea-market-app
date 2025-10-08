<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    /**
     * 受け付けるカラムを明示（$guarded=[] は削除）
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'price',
        'payment_method',
        'quantity',
        'shipping_fee',
        'total',
        'postal_code',
        'address',
        'building_name',
    ];

    /**
     * 型キャスト（数値系を整数に）
     */
    protected $casts = [
        'user_id'      => 'int',
        'item_id'      => 'int',
        'price'        => 'int',
        'quantity'     => 'int',
        'shipping_fee' => 'int',
        'total'        => 'int',
    ];

    /**
     * ユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * 商品
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class);
    }
}
