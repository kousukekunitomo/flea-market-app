@extends('layouts.app')

@section('css')
  @php
    $cssPath = public_path('css/item-show.css');
    $ver = file_exists($cssPath) ? filemtime($cssPath) : time();
  @endphp
  <link rel="stylesheet" href="{{ asset('css/item-show.css') }}?v={{ $ver }}">
@endsection

@section('content')
@php
    $comments    = $item->relationLoaded('comments') ? $item->comments : ($item->comments ?? collect());
    $commentsCnt = $item->comments_count ?? $comments->count();
    $isMyItem    = auth()->check() && (int)$item->user_id === (int)auth()->id();
    $likesCount  = $item->likes_count ?? (method_exists($item, 'likedBy') ? $item->likedBy()->count() : 0);
    $liked       = isset($liked) ? $liked : (auth()->check() ? auth()->user()->hasLiked($item) : false);
@endphp

<div class="show-wrap">
  <div class="show-grid">
    {{-- 左：画像 --}}
    <div class="show-left">
      <div class="show-image">
        <img
          src="{{ $item->image_url }}"
          alt="{{ $item->name }}"
          onerror="this.src='{{ asset('images/placeholder.png') }}'">
      </div>
    </div>

    {{-- 右：本文 --}}
    <div class="show-right">
      {{-- タイトル・ブランド --}}
      <h1 class="show-title">{{ $item->name }}</h1>
      @if(!empty($item->brand_name))
        <div class="brand">{{ $item->brand_name }}</div>
      @endif

      {{-- 価格 --}}
      <div class="show-price-row">
        <div class="show-price">
          ¥{{ number_format($item->price) }} <span class="tax">（税込）</span>
        </div>
      </div>

      {{-- いいね／コメントアイコン --}}
      <div class="show-icons">
        <div class="icon-stack">
          @auth
            <form method="POST" action="{{ route('items.like.toggle', $item) }}">
              @csrf
              <button type="submit" class="icon-btn star {{ $liked ? 'active' : '' }}" aria-label="お気に入り">
                <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                  <path class="star-path" d="M12 2l3.09 6.26L22 9.27l-5 4.88L18.18 22 12 18.6 5.82 22 7 14.15l-5-4.88 6.91-1.01L12 2z"/>
                </svg>
              </button>
            </form>
          @else
            <a href="{{ route('login', ['intended' => url()->current()]) }}" class="icon-btn star" aria-label="ログインしてお気に入り">
              <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
                <path class="star-path" d="M12 2l3.09 6.26L22 9.27l-5 4.88L18.18 22 12 18.6 5.82 22 7 14.15l-5-4.88 6.91-1.01L12 2z"/>
              </svg>
            </a>
          @endauth
          <div class="icon-count">{{ $likesCount }}</div>
        </div>

        <div class="icon-stack">
          <a href="#comments" class="icon-btn bubble" aria-label="コメントへ移動">
            <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true">
              <path class="bubble-path" d="M4 4h16v12H7l-3 3V4z"/>
            </svg>
          </a>
          <div class="icon-count">{{ $commentsCnt }}</div>
        </div>
      </div>

      {{-- 購入ボタン --}}
      <div class="show-cta">
        @auth
          @if($isMyItem)
            <span class="buy-btn is-disabled">購入手続きへ</span>
          @else
            <a href="{{ route('purchase.show', $item) }}" class="buy-btn">購入手続きへ</a>
          @endif
        @else
          <a href="{{ route('login', ['intended' => route('purchase.show', $item)]) }}" class="buy-btn">購入手続きへ</a>
        @endauth
      </div>

      {{-- 商品説明 --}}
      <div class="show-section">
        <div class="sec-title">商品説明</div>
        <div class="desc">{{ $item->description }}</div>
      </div>

      {{-- 商品の情報 --}}
      <div class="show-section">
        <div class="sec-title">商品の情報</div>
        <dl class="info-table">
          <div class="row">
            <dt>カテゴリー</dt>
            <dd>
              @forelse($item->categories ?? [] as $cat)
                <span class="chip">{{ $cat->name }}</span>
              @empty
                <span class="chip">未設定</span>
              @endforelse
            </dd>
          </div>
          <div class="row">
            <dt>商品の状態</dt>
            <dd>{{ $item->condition->condition ?? '不明' }}</dd>
          </div>
        </dl>
      </div>

      {{-- コメント --}}
      <div class="show-section" id="comments">
        <div class="sec-title">コメント（{{ $commentsCnt }}）</div>

        <div class="comment-list">
          @if($commentsCnt > 0)
            @foreach($comments as $c)
              @php
                $avatar = optional($c->user->profile)->image_url ?? asset('images/placeholder.png');
              @endphp
              <div class="comment">
                <div class="comment-head">
                  <img class="comment-avatar" src="{{ $avatar }}" alt="">
                  <div class="comment-name">{{ $c->user->name ?? 'ユーザー' }}</div>
                </div>
                <div class="comment-body">{{ $c->content }}</div>
              </div>
            @endforeach
          @else
            <div class="comment empty">まだコメントはありません。</div>
          @endif
        </div>

        {{-- 入力 --}}
        <div class="comment-form-title">商品へのコメント</div>

        @auth
          <form class="comment-form" method="POST" action="{{ route('items.comments.store', $item) }}">
            @csrf
            <textarea name="content"></textarea>
            <button class="comment-btn" type="submit">コメントを送信する</button>
          </form>
        @else
          {{-- 非ログインでも見た目は同じボタンでログインへ --}}
          <form class="comment-form" method="GET" action="{{ route('login') }}">
            <input type="hidden" name="intended" value="{{ url()->current() }}">
            <textarea disabled></textarea>
            <button class="comment-btn" type="submit" aria-disabled="true">コメントを送信する</button>
          </form>
        @endauth
      </div>
    </div>
  </div>
</div>
@endsection
