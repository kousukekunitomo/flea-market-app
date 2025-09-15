@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    $avatar = optional($user->profile)->image_url ?? asset('images/placeholder.png');
@endphp

@section('css')
  <link rel="stylesheet" href="{{ asset('css/items.css') }}">
  <style>
    /* ==== マイページ：ユーザー情報行 ==== */
    .mypage-head {
      display: flex;
      align-items: center;
      justify-content: space-between; /* 左右に配置 */
      margin: 40px auto 30px;          /* ヘッダーとの間隔と下の余白 */
      max-width: 1200px;               /* 一覧と揃えるための中央寄せ */
      padding: 0 20px;                 /* 両端の余白 */
    }

    .mypage-user {
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .mypage-avatar {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      object-fit: cover;
      background: #eee;
      flex-shrink: 0;
    }

    .mypage-name {
      font-weight: 800;
      font-size: 16px;
      color: #000;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .mypage-edit {
      display: inline-block;
      padding: 8px 14px;
      border: 1px solid #f15b6c;
      color: #f15b6c;
      background: #fff;
      border-radius: 6px;
      font-weight: 700;
      text-decoration: none;
      white-space: nowrap;
    }
    .mypage-edit:hover {
      background: #fff5f6;
    }

    /* スマホ時は縦積み */
    @media (max-width: 640px) {
      .mypage-head {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      .mypage-edit {
        align-self: flex-end;
      }
    }
  </style>
@endsection

@section('content')
<div class="item-page">

  {{-- ユーザー情報ヘッダー --}}
  <div class="mypage-head">
    <div class="mypage-user">
      <img class="mypage-avatar" src="{{ $avatar }}" alt="">
      <div class="mypage-name">{{ $user->name }}</div>
    </div>
    <a href="{{ route('profile.edit') }}" class="mypage-edit">プロフィールを編集</a>
  </div>

  {{-- タブ --}}
  <div class="tab-menu-container">
    <ul class="tab-menu">
      <li class="{{ $tab === 'listed' ? 'active' : '' }}">
        <a href="{{ route('mypage.index', ['tab' => 'listed']) }}">出品した商品</a>
      </li>
      <li class="{{ $tab === 'purchased' ? 'active' : '' }}">
        <a href="{{ route('mypage.index', ['tab' => 'purchased']) }}">購入した商品</a>
      </li>
    </ul>
  </div>

  <div class="tab-divider"></div>

  {{-- 商品一覧（商品画像＋商品名のみ） --}}
  <div class="item-list-wrapper">
    @if($items->count())
      <div class="item-list">
        @foreach ($items as $item)
          <div class="item-card">
            <a href="{{ route('items.show', $item) }}" class="card-link">
              <div class="item-image">
                @php
                  $src = asset('images/placeholder.png');
                  if (!empty($item->image_path)) {
                      $src = Str::startsWith($item->image_path, ['http://','https://'])
                          ? $item->image_path
                          : asset('storage/' . ltrim($item->image_path, '/'));
                  }
                @endphp
                <img src="{{ $src }}" alt="{{ $item->name }}">
              </div>
              <div class="item-info">
                <h3 class="item-name">{{ $item->name }}</h3>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <div style="margin-top:16px;">
        {{ $items->appends(['tab' => $tab])->links() }}
      </div>
    @else
      <p class="empty-state">
        {{ $tab === 'purchased' ? '購入した商品はまだありません。' : '出品した商品はまだありません。' }}
      </p>
    @endif
  </div>
</div>
@endsection
