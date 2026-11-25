@if (session('sukses'))
    <div class="alert alert-sukses" role="status">
        {{ session('sukses') }}
    </div>
@endif

@if (session('galat'))
    <div class="alert alert-galat" role="alert">
        {{ session('galat') }}
    </div>
@endif
