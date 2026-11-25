<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - President's Challenge</title>
    <link rel="stylesheet" href="{{ asset('resources/css/bootstrap.min.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand"
           href="{{ url('/admin/booth-staff') }}">President Challenge Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <ul class="navbar-nav"></ul>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('/admin/booth-staff*')
                        ? 'active' : ''}}"
                   href="{{ url('/admin/booth-staff') }}">Booth Staff</a>
            </li>
        </div>
    </div>
</nav>
<main class="container my-4 flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>
<footer class="bg-light py-3 border-top">
    <div class="container text-muted small">President's Challenge 2027 Module A</div>
</footer>
<script src="{{ asset('resources/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
