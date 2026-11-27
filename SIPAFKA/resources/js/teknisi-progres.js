document.addEventListener('DOMContentLoaded', function () {
    // 1. Filter & pencarian otomatis pada halaman Daftar Tugas Teknisi
    const filterForm = document.getElementById('formFilterTugas');
    const searchInput = document.getElementById('inputCariTugas');
    const statusSelect = document.getElementById('selectStatusTugas');

    if (filterForm) {
        if (statusSelect) {
            statusSelect.addEventListener('change', function () {
                filterForm.submit();
            });
        }

        if (searchInput) {
            let searchTimer = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    filterForm.submit();
                }, 400);
            });
        }
    }

    // 2. Validasi & counter pada halaman Perbarui Status Perbaikan
    const updateForm = document.getElementById('formUpdateProgres');
    const statusField = document.getElementById('status');
    const catatanField = document.getElementById('catatan');
    const charCountEl = document.getElementById('catatanCharCount');
    const errorEl = document.getElementById('catatanClientError');
    const warningBanner = document.getElementById('bannerPeringatanSelesai');

    if (updateForm && catatanField) {
        function refreshCounter() {
            if (!charCountEl) {
                return;
            }
            const length = catatanField.value.trim().length;
            charCountEl.textContent = length + ' karakter';
        }

        function refreshWarningBanner() {
            if (!warningBanner || !statusField) {
                return;
            }
            if (statusField.value === 'Selesai') {
                warningBanner.classList.add('is-selesai-warn');
            } else {
                warningBanner.classList.remove('is-selesai-warn');
            }
        }

        function clearError() {
            catatanField.classList.remove('is-invalid');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.remove('is-visible');
            }
        }

        catatanField.addEventListener('input', function () {
            refreshCounter();
            if (catatanField.value.trim().length >= 10) {
                clearError();
            }
        });

        if (statusField) {
            statusField.addEventListener('change', refreshWarningBanner);
            refreshWarningBanner();
        }

        updateForm.addEventListener('submit', function (event) {
            const value = catatanField.value.trim();
            if (value.length < 10) {
                event.preventDefault();
                catatanField.classList.add('is-invalid');
                if (errorEl) {
                    errorEl.textContent = value.length === 0
                        ? 'Catatan perkembangan wajib diisi.'
                        : 'Catatan perkembangan minimal 10 karakter.';
                    errorEl.classList.add('is-visible');
                }
                catatanField.focus();
            }
        });

        refreshCounter();
    }
});
