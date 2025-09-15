@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    // コントローラから渡される $q を優先。無ければ request('q') を使う。
    $keyword = isset($q) ? $q : request('q');
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')
<div class="item-page">

  {{-- タブ --}}
  <div class="tab-menu-container">
    <ul class="tab-menu">
      <li class="{{ $tab === 'recommend' ? 'active' : '' }}">
        <a href="{{ route('items.index', ['tab' => 'recommend', 'q' => $keyword]) }}">おすすめ</a>
      </li>
      <li class="{{ $tab === 'mylist' ? 'active' : '' }}">
        <a href="{{ route('items.index', ['tab' => 'mylist', 'q' => $keyword]) }}">マイリスト</a>
      </li>
    </ul>
  </div>

  {{-- グレーの下線（画面端まで） --}}
  <div class="tab-divider"></div>

  {{-- 商品一覧 --}}
  <div class="item-list-wrapper">
    @if($items->count())
      <div class="item-list">
        @foreach ($items as $item)
          <div class="item-card">
            {{-- 画像＋テキストを丸ごとリンク --}}
            <a href="{{ route('items.show', $item) }}" class="card-link">
              <div class="item-image">
                @php
                  if (!empty($item->image_path) && Str::startsWith($item->image_path, ['http://','https://'])) {
                      $src = $item->image_path;
                  } elseif (!empty($item->image_path)) {
                      $src = asset('storage/' . $item->image_path);
                  } else {
                      $src = asset('images/placeholder.png');
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

      {{-- ページネーション（検索語＆タブを保持） --}}
      <div style="margin-top:16px;">
        {{ $items->appends(['tab' => $tab, 'q' => $keyword])->links() }}
      </div>
    @else
      <p class="empty-state">
        まだ商品がありません。
        @if($keyword)
          （「{{ $keyword }}」の検索結果は 0 件でした）
        @endif
      </p>
    @endif
  </div>

</div>
@endsection
