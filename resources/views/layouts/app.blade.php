<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Tambahan: font awesome dan bootstrap 4 -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<style>
    body {
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .login-box {
        width: 360px;
        margin: 0 auto;
    }

    .login-logo {
        text-align: center;
        font-weight: 300;
    }

    .card {
        border: 0;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: .25rem;
    }

    .card-body {
        padding: 20px;
    }

    .login-box-msg {
        margin: 0;
        padding: 0 20px 20px;
        text-align: center;
        color: #6c757d;
        font-size: 1rem;
    }

    /* Input group styles */
    .input-group .form-control:focus~.input-group-append .input-group-text,
    .input-group .form-control:focus~.input-group-prepend .input-group-text {
        border-color: #80bdff;
    }

    .input-group-text {
        background-color: transparent;
        border-left: 0;
        color: #777;
    }

    .form-control {
        border-right: 0;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #80bdff;
    }

    /* saat input ERROR (.is-invalid) */
    .input-group .form-control.is-invalid~.input-group-append .input-group-text,
    .input-group .form-control.is-invalid:focus~.input-group-append .input-group-text,
    .input-group .form-control.is-invalid~.input-group-prepend .input-group-text,
    .input-group .form-control.is-invalid:focus~.input-group-prepend .input-group-text {
        border-color: #dc3545 !important;
        color: #dc3545;
    }

    /* menghilangkan glow merah & mencegah border kanan terpotong/bertumpuk */
    .form-control.is-invalid,
    .form-control.is-invalid:focus {
        box-shadow: none !important;
        border-color: #dc3545 !important;
        border-right: 0 !important;
        background-image: none !important;
    }

    /* Button styles */
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    .social-auth-links i {
        margin-right: 5px;
    }

    .divider-text {
        text-align: center;
        margin: 15px 0;
        color: #6c757d;
        font-size: 14px;
    }

    /* Links */
    p a {
        color: #007bff;
        text-decoration: underline;
    }

    p a:hover {
        color: #0056b3;
    }

    .mb-1 {
        margin-bottom: 0.25rem !important;
    }

    .mb-0 {
        margin-bottom: 0 !important;
    }
</style>

<body>
    <div id="app">

        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
