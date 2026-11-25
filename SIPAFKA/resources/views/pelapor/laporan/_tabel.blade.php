<div class="table-wrap">
    <table class="sip-table">
        <thead>
            <tr>
                <th>No. Laporan</th>
                <th>Laporan &amp; Lokasi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporanList as $item)
                <tr>
                    <td class="pl-col-nomor">{{ $item->nomor_laporan }}</td>
                    <td>
                        <div class="pl-row-title">{{ $item->judul }}</div>
                        <div class="pl-row-sub">
                            {{ $item->fasilitas?->ruang ?? '-' }}@if ($item->fasilitas?->gedung), {{ $item->fasilitas->gedung }}@endif
                        </div>
                    </td>
                    <td class="pl-col-date">{{ $item->tanggal?->translatedFormat('d M Y') }}</td>
                    <td>
                        <x-status-badge :status="$item->status" />
                    </td>
                    <td>
                        <a href="{{ route('pelapor.laporan.show', $item) }}" class="pl-action-link">
                            Lihat &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="pl-empty-cell">
                        Belum ada laporan kerusakan yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
