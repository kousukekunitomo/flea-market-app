@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')
<div class="item-page">

    {{-- タブ --}}
    <div class="tab-menu-container">
        <ul class="tab-menu">
            <li class="{{ $tab === 'recommend' ? 'active' : '' }}">
                <a href="{{ route('items.index', ['tab' => 'recommend']) }}">おすすめ</a>
            </li>
            <li class="{{ $tab === 'mylist' ? 'active' : '' }}">
                <a href="{{ route('items.index', ['tab' => 'mylist']) }}">マイリスト</a>
            </li>
        </ul>
    </div>

    {{-- グレーの下線（画面端まで） --}}
    <div class="tab-divider"></div>

    {{-- 商品一覧：左寄せで表示 --}}
    <div class="item-list-wrapper">
        <div class="item-list">
            @foreach ($items as $item)
                <div class="item-card">
                    <div class="item-image">
                        @if (Str::startsWith($item->image_path, 'http'))
                            <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
                        @else
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @endif
                    </div>
                    <div class="item-info">
                        <h3 class="item-name">{{ $item->name }}</h3>
                        <p class="item-description">{{ $item->description }}</p>
                        <p class="item-price">価格: ¥{{ number_format($item->price) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
