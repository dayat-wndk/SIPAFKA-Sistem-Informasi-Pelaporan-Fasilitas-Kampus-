<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — SIPAFKA FMIPA UHO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sipafka.css') }}">
</head>
<body class="auth-body">
    <div class="auth-split">
        @include('auth._hero')

        <section class="auth-form-pane">
            <div class="auth-card">
                <div class="auth-eyebrow">PENDAFTARAN AKUN PELAPOR</div>
                <h2 class="auth-heading">Buat akun baru</h2>
                <p class="auth-sub">Daftar sebagai pelapor untuk melaporkan kerusakan fasilitas FMIPA UHO.</p>

                @if ($errors->any())
                    <div class="alert alert-galat" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="auth-form" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Budi Santoso"
                            maxlength="120"
                            required
                            autofocus
                            class="form-control @error('name') is-invalid @enderror"
                        >
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@uho.ac.id"
                            maxlength="150"
                            required
                            class="form-control @error('email') is-invalid @enderror"
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Kata sandi</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            required
                            class="form-control @error('password') is-invalid @enderror"
                        >
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi kata sandi"
                            minlength="8"
                            required
                            class="form-control"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Daftar akun</button>
                </form>

                <div class="auth-footer-note">
                    <p class="auth-switch">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
                    </p>
                    <p>Akun Admin dan Teknisi tetap dikelola oleh Admin FMIPA.</p>
                    <p class="auth-copyright">SIPAFKA &bull; FMIPA Universitas Halu Oleo</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
