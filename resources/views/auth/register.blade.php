@extends('layouts.auth')
@section('title', 'Register')
@section('content')
<div class="auth-card">
    <div style="text-align:center; margin-bottom:30px;">
        <div class="auth-logo">
            <div class="auth-logo-icon"><i class="fas fa-coffee"></i></div>
            <div class="auth-logo-text">CafePOS</div>
        </div>
        <p style="color:var(--neutral-light); margin-top:10px;">Daftar akun customer</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label class="form-label"><i class="fas fa-user"></i> Username</label>
            <input type="text" name="username" class="form-control"
                   value="{{ old('username') }}" placeholder="Username" required>
        </div>
        <div class="form-group">
            <label class="form-label"><i class="fas fa-id-card"></i> Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control"
                   value="{{ old('nama_lengkap') }}" placeholder="Nama lengkap" required>
        </div>
        <div class="form-group">
            <label class="form-label"><i class="fas fa-envelope"></i> Email (opsional)</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="Email">
        </div>
        <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Minimal 6 karakter" required>
        </div>
        <div class="form-group">
            <label class="form-label"><i class="fas fa-lock"></i> Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control"
                   placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="action-btn action-btn-primary"
                style="width:100%; justify-content:center; padding:15px; font-size:16px;">
            <i class="fas fa-user-plus"></i> Daftar Sekarang
        </button>

        <div style="text-align:center; margin-top:20px; color:var(--neutral-light); font-size:14px;">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:var(--gold-main); font-weight:600;">Login</a>
        </div>
    </form>
</div>
@endsection