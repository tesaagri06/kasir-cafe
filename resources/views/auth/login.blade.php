@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="auth-card">

    {{-- Logo --}}
    <div style="text-align:center; margin-bottom:28px;">
        <div class="auth-logo">
            <div class="auth-logo-icon"><i class="fas fa-coffee"></i></div>
            <div class="auth-logo-text">CafePOS</div>
        </div>
        <p class="auth-subtitle">Masuk ke sistem kasir</p>
    </div>

    {{-- Error messages --}}
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-user"></i> Username
            </label>
            <input type="text"
                   name="username"
                   class="form-control"
                   value="{{ old('username') }}"
                   placeholder="Masukkan username"
                   required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label">
                <i class="fas fa-lock"></i> Password
            </label>
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Masukkan password"
                   required>
        </div>

        <button type="submit" class="action-btn action-btn-primary auth-submit-btn">
            <i class="fas fa-sign-in-alt"></i> LOGIN
        </button>
    </form>

    <div class="auth-footer">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar di sini</a>
    </div>

</div>
@endsection