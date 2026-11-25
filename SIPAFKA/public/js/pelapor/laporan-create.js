document.addEventListener('DOMContentLoaded', function () {
    const dataScript = document.getElementById('fasilitasDataJson');
    const selectFasilitas = document.getElementById('fasilitasNama');
    const selectLokasi = document.getElementById('fasilitasLokasi');
    const inputFoto = document.getElementById('foto');
    const fotoPreview = document.getElementById('fotoPreview');
    const fotoClientError = document.getElementById('fotoClientError');
    const uploadBox = document.getElementById('fotoUploadBox');

    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    const ALLOWED_TYPES = ['image/jpeg', 'image/png'];
    const ALLOWED_EXTS = ['jpg', 'jpeg', 'png'];

    let fasilitasList = [];
    if (dataScript) {
        try {
            fasilitasList = JSON.parse(dataScript.textContent || '[]');
        } catch (e) {
            fasilitasList = [];
        }
    }

    function renderLokasiOptions(namaDipilih, selectedId) {
        if (!selectLokasi) {
            return;
        }

        selectLokasi.innerHTML = '';

        if (!namaDipilih) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Pilih fasilitas terlebih dahulu';
            selectLokasi.appendChild(opt);
            selectLokasi.disabled = true;
            return;
        }

        const cocok = fasilitasList.filter(function (item) {
            return item.nama === namaDipilih;
        });

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Pilih lokasi';
        selectLokasi.appendChild(defaultOpt);

        cocok.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = String(item.id);
            opt.textContent = item.label;
            if (selectedId && String(selectedId) === String(item.id)) {
                opt.selected = true;
            }
            selectLokasi.appendChild(opt);
        });

        selectLokasi.disabled = false;
    }

    if (selectFasilitas && selectLokasi) {
        const oldId = selectLokasi.dataset.oldId || '';
        renderLokasiOptions(selectFasilitas.value, oldId);

        selectFasilitas.addEventListener('change', function () {
            renderLokasiOptions(selectFasilitas.value, '');
        });
    }

    function formatUkuran(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(1).replace('.', ',') + ' MB';
        }
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function tampilkanErrorFoto(pesan) {
        if (fotoClientError) {
            fotoClientError.textContent = pesan;
            fotoClientError.classList.remove('pl-hidden');
        }
        if (uploadBox) {
            uploadBox.classList.add('is-invalid');
        }
        if (fotoPreview) {
            fotoPreview.innerHTML = '<span class="pl-upload-placeholder">Pilih file foto kerusakan (JPG atau PNG, maksimal 5 MB)</span>';
        }
    }

    function bersihkanErrorFoto() {
        if (fotoClientError) {
            fotoClientError.textContent = '';
            fotoClientError.classList.add('pl-hidden');
        }
        if (uploadBox) {
            uploadBox.classList.remove('is-invalid');
        }
    }

    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            bersihkanErrorFoto();

            const file = inputFoto.files && inputFoto.files[0];
            if (!file) {
                if (fotoPreview) {
                    fotoPreview.innerHTML = '<span class="pl-upload-placeholder">Pilih file foto kerusakan (JPG atau PNG, maksimal 5 MB)</span>';
                }
                return;
            }

            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const mimeValid = ALLOWED_TYPES.includes(file.type) || ALLOWED_EXTS.includes(ext);

            if (!mimeValid) {
                inputFoto.value = '';
                tampilkanErrorFoto('Format foto harus JPG, JPEG, atau PNG.');
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                inputFoto.value = '';
                tampilkanErrorFoto('Ukuran foto maksimal 5 MB.');
                return;
            }

            if (fotoPreview) {
                fotoPreview.innerHTML = '';
                const span = document.createElement('span');
                span.className = 'pl-upload-file-meta';
                span.textContent = file.name + ' \u2022 ' + formatUkuran(file.size);
                fotoPreview.appendChild(span);
            }
        });
    }
});
