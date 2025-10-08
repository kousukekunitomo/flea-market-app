<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Eloquent relation types
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Related models
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Comment;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     *
     * Add 'stripe_customer_id' if you sometimes update it via fill/update().
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_customer_id',
    ];

    /**
     * Attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Profile (1:1)
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Items the user listed (1:N)
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Purchases (history records) (1:N)
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Items the user purchased (through purchases pivot)
     */
    public function purchasedItems(): BelongsToMany
    {
        // If the purchases table does NOT have timestamps, remove ->withTimestamps()
        return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id')
                    ->withTimestamps();
    }

    /**
     * Items the user liked (through likes pivot)
     */
    public function likedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id');
    }

    /**
     * Comments the user posted (1:N)
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Helper: whether the user liked a given item
     */
    public function hasLiked(Item $item): bool
    {
        return $this->likedItems()
            ->where('items.id', $item->id)
            ->exists();
    }
}
