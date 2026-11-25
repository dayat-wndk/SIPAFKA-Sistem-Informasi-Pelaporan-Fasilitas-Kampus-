document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formFilterRiwayat');
    const selectStatus = document.getElementById('selectStatus');
    const inputCari = document.getElementById('inputCari');

    if (!form) {
        return;
    }

    if (selectStatus) {
        selectStatus.addEventListener('change', function () {
            form.submit();
        });
    }

    if (inputCari) {
        let timer = null;
        inputCari.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                form.submit();
            }, 450);
        });
    }
});
