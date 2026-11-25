@php
    use App\Enums\StatusLaporan;

    $status = $laporan->status;
    $statusValue = $status instanceof StatusLaporan ? $status->value : (string) $status;
    $ditolak = $statusValue === StatusLaporan::Ditolak->value;

    $urutan = [
        StatusLaporan::MenungguVerifikasi->value => 1,
        StatusLaporan::Diverifikasi->value => 2,
        StatusLaporan::Ditugaskan->value => 3,
        StatusLaporan::Diproses->value => 4,
        StatusLaporan::Selesai->value => 5,
    ];
    $tahapSekarang = $urutan[$statusValue] ?? 1;
@endphp

@if ($ditolak)
    <ol class="pl-timeline">
        <li class="pl-timeline-item is-done">
            <span class="pl-timeline-dot">1</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Menunggu Verifikasi</div>
                <div class="pl-timeline-sub">
                    {{ $laporan->tanggal?->translatedFormat('d M Y') }} &mdash; Laporan diterima
                </div>
            </div>
        </li>

        <li class="pl-timeline-item is-rejected">
            <span class="pl-timeline-dot">&times;</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Ditolak</div>
                <div class="pl-timeline-sub">
                    {{ ($laporan->diverifikasi_at ?? $laporan->updated_at)?->translatedFormat('d M, H.i') }}
                    &mdash; {{ $laporan->admin?->name ?? 'Admin FMIPA' }}
                </div>
                @if ($laporan->alasan_penolakan)
                    <div class="pl-timeline-reject-note">{{ $laporan->alasan_penolakan }}</div>
                @endif
            </div>
        </li>
    </ol>
@else
    <ol class="pl-timeline">
        <li class="pl-timeline-item {{ $tahapSekarang >= 1 ? 'is-done' : 'is-pending' }}">
            <span class="pl-timeline-dot">1</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Menunggu Verifikasi</div>
                <div class="pl-timeline-sub">
                    {{ $laporan->tanggal?->translatedFormat('d M Y') }} &mdash; Laporan diterima
                </div>
            </div>
        </li>

        <li class="pl-timeline-item {{ $tahapSekarang >= 2 ? 'is-done' : 'is-pending' }}">
            <span class="pl-timeline-dot">2</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Diverifikasi</div>
                <div class="pl-timeline-sub">
                    @if ($tahapSekarang >= 2)
                        {{ $laporan->diverifikasi_at?->translatedFormat('d M, H.i') ?? '-' }}
                        &mdash; {{ $laporan->admin?->name ?? 'Admin FMIPA' }}
                    @else
                        Menunggu verifikasi dari Admin FMIPA.
                    @endif
                </div>
            </div>
        </li>

        <li class="pl-timeline-item {{ $tahapSekarang >= 3 ? 'is-done' : 'is-pending' }}">
            <span class="pl-timeline-dot">3</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Ditugaskan</div>
                <div class="pl-timeline-sub">
                    @if ($tahapSekarang >= 3 && $laporan->penugasan)
                        {{ $laporan->penugasan->tanggal_tugas?->translatedFormat('d M, H.i') ?? '-' }}
                        &mdash; {{ $laporan->penugasan->teknisi?->name ?? 'Teknisi' }}
                    @else
                        Menunggu penugasan teknisi oleh admin.
                    @endif
                </div>
            </div>
        </li>

        <li class="pl-timeline-item {{ $tahapSekarang >= 4 ? 'is-done' : 'is-pending' }}">
            <span class="pl-timeline-dot">4</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Diproses</div>
                <div class="pl-timeline-sub">
                    @if ($tahapSekarang >= 4)
                        {{ ($perbaikanPertama?->tanggal ?? $laporan->penugasan?->tanggal_tugas)?->translatedFormat('d M, H.i') ?? '-' }}
                        &mdash; Perbaikan berjalan
                    @else
                        Menunggu teknisi memulai perbaikan.
                    @endif
                </div>
            </div>
        </li>

        <li class="pl-timeline-item {{ $tahapSekarang >= 5 ? 'is-done' : 'is-pending' }}">
            <span class="pl-timeline-dot">5</span>
            <div class="pl-timeline-body">
                <div class="pl-timeline-title">Selesai</div>
                <div class="pl-timeline-sub">
                    @if ($tahapSekarang >= 5)
                        {{ ($perbaikanSelesai?->tanggal ?? $perbaikanTerakhir?->tanggal)?->translatedFormat('d M, H.i') ?? '-' }}
                        &mdash; {{ $perbaikanSelesai?->hasil ?? 'Perbaikan selesai dituntaskan' }}
                    @else
                        Selesai akan tercatat setelah teknisi menuntaskan perbaikan.
                    @endif
                </div>
            </div>
        </li>
    </ol>
@endif
