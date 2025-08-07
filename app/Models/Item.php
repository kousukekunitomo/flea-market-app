<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'brand_name',
        'description',
        'image_path',     // ✅ 実際に使われている画像カラム
        'condition_id',   // ✅ 条件の外部キー
        'category_id',    // ✅ カテゴリを扱う場合はこちらも
        'user_id',        // ✅ 出品者（ログインユーザー）のID
        'status',         // ✅ ステータス（販売中など）
    ];
}
