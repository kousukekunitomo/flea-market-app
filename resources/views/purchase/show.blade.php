{{-- resources/views/purchase/show.blade.php --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v={{ filemtime(public_path('css/purchase.css')) }}">
@endsection

@section('content')
@php
    use Illuminate\Support\Str;

    $thumb = $item->image_path
        ? (Str::startsWith($item->image_path, ['http://','https://']) ? $item->image_path : asset('storage/'.$item->image_path))
        : asset('images/placeholder.png');

    // プロフィール（配送先の初期値）
    $postal   = old('postal_code',   optional($profile)->postal_code   ?? '');
    $address  = old('address',       optional($profile)->address       ?? '');
    $building = old('building_name', optional($profile)->building_name ?? '');

    // ▼ Stripe用に pay_method を見る
    $payOld   = old('pay_method', 'convenience_store');
    $payLabel = $payOld === 'credit_card' ? 'クレジットカード' : ($payOld === 'bank_transfer' ? '銀行振込' : 'コンビニ払い');
@endphp

<div class="purchase-wrap">

  @if ($errors->any())
    <div class="purchase-errors">
      <ul>
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- フォーム全体（Stripe Checkoutへ） --}}
  <form method="POST" action="{{ route('purchase.checkout', $item) }}" class="purchase-grid" id="purchase-form">
    @csrf

    {{-- 左カラム --}}
    <div class="purchase-left">

      {{-- 商品ボックス --}}
      <div class="purchase-itembox">
        <div class="purchase-thumb">
          <img src="{{ $thumb }}" alt="{{ $item->name }}">
        </div>
        <div class="purchase-iteminfo">
          <div class="purchase-itemname">{{ $item->name }}</div>
          <div class="purchase-itemprice">¥{{ number_format($item->price) }}</div>
        </div>
      </div>

      {{-- 数量は常に 1（UI 非表示） --}}
      <input type="hidden" name="quantity" value="1">

      {{-- 支払い方法 --}}
      <div class="purchase-block pay-block">
        <div class="purchase-blocktitle">支払い方法</div>
        <div class="purchase-field">
          {{-- ▼ name を pay_method に変更（controller のバリデーションと一致） --}}
          <select name="pay_method" class="purchase-select" id="js-pay">
            <option value="convenience_store" {{ $payOld==='convenience_store' ? 'selected' : '' }}>コンビニ払い</option>
            <option value="credit_card"       {{ $payOld==='credit_card'       ? 'selected' : '' }}>カード支払い</option>
            <option value="bank_transfer"     {{ $payOld==='bank_transfer'     ? 'selected' : '' }}>銀行振込</option>
          </select>
        </div>
        {{-- ▼ エラーキーも pay_method に --}}
        @error('pay_method') <div class="purchase-error">{{ $message }}</div> @enderror
      </div>

      {{-- 配送先 --}}
      <div class="purchase-block ship-block">
        <div class="purchase-blocktitle purchase-flex">
          <span>配送先</span>
          <a href="{{ route('address.edit', ['item' => $item->id]) }}" class="purchase-editlink">変更する</a>
        </div>

        <div class="purchase-address">
          <div class="purchase-addressline">〒 {{ $postal ?: 'XXX-XXXX' }}</div>
          <div class="purchase-addressline">{{ $address ?: 'ここに住所が表示されます' }}</div>
          @if($building)
            <div class="purchase-addressline">{{ $building }}</div>
          @endif
        </div>

        {{-- 送信用 hidden（Stripeの確定はWebhookでプロフィール参照なので無くてもOK） --}}
        <input type="hidden" name="postal_code"    value="{{ $postal }}">
        <input type="hidden" name="address"        value="{{ $address }}">
        <input type="hidden" name="building_name"  value="{{ $building }}">

        @error('postal_code') <div class="purchase-error">{{ $message }}</div> @enderror
        @error('address')     <div class="purchase-error">{{ $message }}</div> @enderror
      </div>

    </div>

    {{-- 右カラム（サマリー＋ボタン） --}}
    <div class="purchase-right">
      <div class="purchase-summary">
        <div class="purchase-summary-row">
          <div class="label">商品代金</div>
          <div class="value">¥{{ number_format($item->price) }}</div>
        </div>
        <div class="purchase-summary-row">
          <div class="label">支払い方法</div>
          <div class="value" id="js-paylabel">{{ $payLabel }}</div>
        </div>
      </div>

      <button type="submit" class="purchase-submit">購入する</button>
    </div>

  </form>
</div>
@endsection

@section('js')
<script>
  // 支払い方法ラベルの同期
  const paySel   = document.getElementById('js-pay');
  const payLabel = document.getElementById('js-paylabel');

  function updatePayLabel(val){
    payLabel.textContent =
      val === 'credit_card'   ? 'カード支払い' :
      val === 'bank_transfer' ? '銀行振込' : 'コンビニ払い';
  }

  if (paySel) {
    updatePayLabel(paySel.value);
    paySel.addEventListener('change', () => updatePayLabel(paySel.value));
  }
</script>
@endsection
