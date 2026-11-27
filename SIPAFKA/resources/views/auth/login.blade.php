<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SIPAFKA FMIPA UHO</title>
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
                <div class="auth-eyebrow">PORTAL LAYANAN FASILITAS</div>
                <h2 class="auth-heading">Selamat datang</h2>
                <p class="auth-sub">Masuk untuk melaporkan dan menangani kerusakan fasilitas kampus.</p>

                <div class="auth-role-tabs" role="tablist" aria-label="Peran pengguna">
                    <button type="button" class="auth-role-tab is-active" data-role-tab="pelapor">Pelapor</button>
                    <button type="button" class="auth-role-tab" data-role-tab="admin">Admin</button>
                    <button type="button" class="auth-role-tab" data-role-tab="teknisi">Teknisi</button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-galat" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('sukses'))
                    <div class="alert alert-sukses" role="status">
                        {{ session('sukses') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@uho.ac.id"
                            required
                            autofocus
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
                            placeholder="Masukkan kata sandi"
                            required
                            class="form-control @error('password') is-invalid @enderror"
                        >
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </form>

                <div class="auth-footer-note">
                    <p class="auth-switch">
                        Belum punya akun pelapor?
                        <a href="{{ route('register') }}" class="auth-link">Daftar sekarang</a>
                    </p>
                    <p>Akun Admin dan Teknisi dikelola oleh Admin FMIPA.<br>Hubungi admin jika mengalami kendala akses.</p>
                    <p class="auth-copyright">SIPAFKA &bull; FMIPA Universitas Halu Oleo</p>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-role-tab]').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('[data-role-tab]').forEach(function (btn) {
                    btn.classList.remove('is-active');
                });
                tab.classList.add('is-active');
            });
        });
    </script>
</body>
</html>
