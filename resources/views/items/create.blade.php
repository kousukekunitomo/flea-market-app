@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h2 class="page-title">商品の出品</h2>

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        {{-- 商品画像 --}}
<div class="form-section">
    <label class="form-label">商品画像</label>
    <div class="image-upload-box">
        <label class="upload-button">
            画像を選択する
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
        </label>
        <div class="image-preview-wrapper">
            <img id="preview" class="image-preview" alt="プレビュー画像">
        </div>
    </div>
</div>






        {{-- カテゴリ（単一選択） --}}
<div class="form-section">
    <label class="form-label">カテゴリー</label>
    <div class="category-buttons" id="category-container">
        @foreach ($categories as $category)
            <label class="category-label">
                <input type="radio" name="category_id" value="{{ $category->id }}"
                    {{ old('category_id') == $category->id ? 'checked' : '' }}>
                {{ $category->name }}
            </label>
        @endforeach
    </div>
</div>

        {{-- 商品の状態 --}}
        <div class="form-section">
            <label class="form-label">商品の状態</label>
            <select name="condition_id" class="form-select">
    <option value="" disabled selected hidden>選択してください</option>
    @foreach ($conditions as $condition)
        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
            {{ $condition->condition }}
        </option>
    @endforeach
</select>

        </div>

<h3 class="section-heading">商品名と説明</h3>


        {{-- 商品名 --}}
        <div class="form-section">
            <label class="form-label">商品名</label>
            <input type="text" name="name" class="form-input" value="{{ old('name') }}">
        </div>

        {{-- ブランド名 --}}
        <div class="form-section">
            <label class="form-label">ブランド名</label>
            <input type="text" name="brand" class="form-input" value="{{ old('brand') }}">
        </div>

        {{-- 商品の説明 --}}
        <div class="form-section">
            <label class="form-label">商品の説明</label>
            <textarea name="description" class="form-textarea">{{ old('description') }}</textarea>
        </div>

        {{-- 販売価格 --}}
        <div class="form-section">
    <label class="form-label">販売価格</label>
    <div class="price-input">
        <input type="number" name="price" class="form-input" value="{{ old('price') }}">
    </div>
</div>


        {{-- 出品ボタン --}}
        <div class="form-section">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            preview.src = '';
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const radios = document.querySelectorAll('input[name="category_id"]');
        let selected = null;

        radios.forEach(radio => {
            radio.addEventListener('click', function () {
                if (selected === this) {
                    this.checked = false;
                    selected = null;
                } else {
                    selected = this;
                }
            });

            // ページロード時に old('category_id') がある場合
            if (radio.checked) {
                selected = radio;
            }
        });
    });
</script>
@endsection


