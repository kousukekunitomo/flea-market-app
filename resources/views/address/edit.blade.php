@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}?v={{ filemtime(public_path('css/address.css')) }}">
@endsection

@section('content')
<div class="address-wrap">
  <h2 class="address-title">配送先の変更</h2>

  @if (session('status'))
    <div class="flash ok">{{ session('status') }}</div>
  @endif

  <div class="address-card">
    {{-- ★ $item を必ず渡す --}}
    <form method="POST" action="{{ route('address.update', $item) }}" class="address-form">
      @csrf
      {{-- ★ ルートは POST のためメソッド偽装は不要 --}}
      {{-- @method('PATCH') は削除 --}}

      <div class="form-rows">
        {{-- 郵便番号 --}}
        <div class="form-row">
          <label class="form-label" for="postal_code">郵便番号</label>
          <div>
            <input id="postal_code" name="postal_code" type="text"
              value="{{ old('postal_code', $profile->postal_code) }}"
              class="input @error('postal_code') is-error @enderror"
              placeholder="123-4567" inputmode="numeric" autocomplete="postal-code">
            @error('postal_code') <div class="error-text">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- 住所 --}}
        <div class="form-row">
          <label class="form-label" for="address">住所</label>
          <div>
            <input id="address" name="address" type="text"
              value="{{ old('address', $profile->address) }}"
              class="input @error('address') is-error @enderror"
              placeholder="都道府県・市区町村・番地" autocomplete="street-address">
            @error('address') <div class="error-text">{{ $message }}</div> @enderror
          </div>
        </div>

        {{-- 建物名（任意） --}}
        <div class="form-row">
          <label class="form-label" for="building_name">建物名</label>
          <div>
            <input id="building_name" name="building_name" type="text"
              value="{{ old('building_name', $profile->building_name) }}"
              class="input @error('building_name') is-error @enderror"
              placeholder="マンション名・部屋番号など">
            @error('building_name') <div class="error-text">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-label"></div>
          <div><button type="submit" class="btn-submit">更新する</button></div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
