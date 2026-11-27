document.addEventListener('DOMContentLoaded', function () {
    // Filter otomatis pada halaman Laporan Masuk
    const filterForm = document.getElementById('formFilterAdminLaporan');
    const selectStatus = document.getElementById('selectStatus');
    const selectFasilitas = document.getElementById('selectFasilitas');
    const inputCari = document.getElementById('inputCari');

    if (filterForm) {
        if (selectStatus) {
            selectStatus.addEventListener('change', function () {
                filterForm.submit();
            });
        }

        if (selectFasilitas) {
            selectFasilitas.addEventListener('change', function () {
                filterForm.submit();
            });
        }

        if (inputCari) {
            let debounceTimer = null;
            inputCari.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 450);
            });
        }
    }

    // Validasi sisi klien saat menolak laporan pada halaman Verifikasi Laporan
    const formVerifikasi = document.getElementById('formVerifikasi');
    const btnTolak = document.getElementById('btnTolakLaporan');
    const alasanInput = document.getElementById('alasanPenolakanInput');
    const clientRejectError = document.getElementById('clientRejectError');

    if (formVerifikasi && btnTolak && alasanInput) {
        alasanInput.addEventListener('input', function () {
            if (alasanInput.value.trim().length >= 5) {
                alasanInput.classList.remove('is-invalid');
                if (clientRejectError) {
                    clientRejectError.textContent = '';
                    clientRejectError.classList.add('adm-hidden');
                }
            }
        });

        btnTolak.addEventListener('click', function (event) {
            const nilai = alasanInput.value.trim();

            if (nilai.length < 5) {
                event.preventDefault();
                alasanInput.classList.add('is-invalid');
                if (clientRejectError) {
                    clientRejectError.textContent =
                        nilai.length === 0
                            ? 'Alasan penolakan wajib diisi sebelum menolak laporan.'
                            : 'Alasan penolakan minimal 5 karakter.';
                    clientRejectError.classList.remove('adm-hidden');
                }
                alasanInput.focus();
            }
        });
    }
});
