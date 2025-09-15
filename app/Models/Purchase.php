<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id', 'item_id', 'quantity',
        'price', 'shipping_fee', 'total',
        'postal_code', 'address', 'building_name',
    ];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function item() { return $this->belongsTo(\App\Models\Item::class); }
}
