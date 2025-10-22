<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait; // トレイト
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Relations
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Comment;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // トレイトの元メソッドに別名を付けて呼べるようにする
    use MustVerifyEmailTrait {
        sendEmailVerificationNotification as protected sendEmailVerificationNotificationFromTrait;
    }

    /** Mass assignable attributes */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_customer_id',
    ];

    /** Hidden attributes */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Casts */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* ================= Relations ================= */

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchasedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id')
                    ->withTimestamps();
    }

    public function likedItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function hasLiked(Item $item): bool
    {
        return $this->likedItems()->where('items.id', $item->id)->exists();
    }

    /* ============== 認証メール送信（重複ガード付き） ============== */

    /**
     * 送信をこの HTTP リクエスト内で一度に限定し、ログも出す
     */
    public function sendEmailVerificationNotification()
    {
        // リクエスト内重複ガード
        $key = 'verification.mail.sent.user_id';
        $container = app();

        if ($container->bound($key) && (int) $container->make($key) === (int) $this->id) {
            \Log::info('DBG sendEmailVerificationNotification:skipped-duplicate', [
                'user_id' => $this->id,
                'email'   => $this->email,
                'time'    => microtime(true),
            ]);
            return; // 2回目は送らない
        }

        // フラグセット（このリクエスト中は2回目以降スキップ）
        $container->instance($key, (int) $this->id);

        \Log::info('DBG sendEmailVerificationNotification:called', [
            'user_id' => $this->id,
            'email'   => $this->email,
            'time'    => microtime(true),
        ]);

        // トレイトの既定実装で実際に送信
        return $this->sendEmailVerificationNotificationFromTrait();
    }
}
