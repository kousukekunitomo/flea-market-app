<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 追加: リレーションの型ヒント
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// 追加: 関連モデルの use
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // プロフィール
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // 出品した商品
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    // 購入（履歴）
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // いいね済み（お気に入り）アイテム
    public function likedItems()
{
    // ★ こちらも withTimestamps() を外す
    return $this->belongsToMany(\App\Models\Item::class, 'likes', 'user_id', 'item_id');
}

    // 便利メソッド（任意）
    public function hasLiked(Item $item): bool
    {
        return $this->likedItems()->where('items.id', $item->id)->exists();
    }

    public function comments()
{
    return $this->hasMany(\App\Models\Comment::class);
}

}
