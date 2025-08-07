<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Flea Market App') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
    <div class="logo">
        <img src="{{ asset('logo.svg') }}" alt="COACHTECHロゴ">
    </div>

    <form class="search-form">
        <input type="text" placeholder="なにをお探しですか？">
    </form>

    <nav class="nav-menu">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-button">ログアウト</button>
        </form>
        <a href="{{ route('profile.edit') }}" class="nav-link">マイページ</a>
        <a href="{{ route('items.create') }}" class="nav-button white">出品</a>
    </nav>
</header>





    <main class="main">
        @yield('content')
    </main>

    @yield('js')
</body>
</html>
