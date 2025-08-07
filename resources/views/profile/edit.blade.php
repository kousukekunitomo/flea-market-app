@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2 class="profile-title">プロフィール設定</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf

        {{-- プロフィール画像 --}}
        <div class="image-upload">
            <img
                src="{{ $profile && $profile->profile_image ? asset('storage/' . $profile->profile_image) : asset('images/placeholder.png') }}"
                class="profile-image"
                id="preview"
                alt=""
            >
            <label class="image-button">
    画像を選択する
    <input type="file" name="profile_image" id="profile_image_input" accept="image/*">
</label>

        </div>

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', $profile->building_name ?? '') }}">
            @error('building_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection

@section('js')
<script>
    document.getElementById('profile_image_input').addEventListener('change', function (event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('preview').src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection
