@extends('layouts.app')

@section('content')
    <div class="login-box">
        <div class="card">
            <div class="login-logo mt-4">
                <img src="{{ asset('images/wellnobekgron.png') }}" alt="Logo" style="width: 100px; height: auto;">
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Selamat Datang di <b>Maira Inventory</b></p>
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="input-group mb-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                            name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                            autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text @error('email') border-danger text-danger @enderror">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group mb-4">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password" name="password" id="password" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text @error('password') border-danger text-danger @enderror">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Ingatkan Saya dan Lupa Sandi- OPSIONAL -->
                    {{-- <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div> --}}
                    {{-- @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif --}}

                    <!-- /.col -->
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                    <!-- /.col -->
            </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
    </div>
    <!-- /.login-box -->
@endsection
