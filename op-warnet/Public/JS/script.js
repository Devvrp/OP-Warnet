$(function() {
    let pcActivityLogs = {};
    const safeSites = ['youtube.com', 'google.com', 'facebook.com', 'tokopedia.com', 'kompas.com', 'detik.com', 'wikipedia.org'];
    const unsafeSites = ['situs-terlarang.com', 'website-phising.net', 'konten-ilegal.org'];
    function updateTimers() {
        $('.countdown-timer').each(function() {
            let endTime = $(this).data('endtime');
            let now = Math.floor(Date.now() / 1000);
            let secondsRemaining = endTime - now;
            let pcCard = $(this).closest('.pc-card');
            let sessionId = pcCard.data('sessionid');
            if (secondsRemaining <= 0) {
                $(this).html("Waktu Habis");
                pcCard.removeClass('digunakan').addClass('tersedia').find('.card-text.fw-bold').removeClass('text-warning-emphasis').addClass('text-success').text('Tersedia');
                pcCard.find('.mt-2').remove();
                pcCard.find('.pc-alert-icon').addClass('d-none');
                if (sessionId && pcActivityLogs[sessionId]) {
                    delete pcActivityLogs[sessionId];
                }
            } else {
                let hours = Math.floor(secondsRemaining / 3600);
                let minutes = Math.floor((secondsRemaining % 3600) / 60);
                let seconds = secondsRemaining % 60;
                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                $(this).html(hours + ':' + minutes + ':' + seconds);
            }
        });
    }
    setInterval(updateTimers, 1000);
    updateTimers();
    $('.pc-card.tersedia').on('click', function() {
        if ($(this).find('small').text() === 'Klik untuk memulai sesi') {
            const computerId = $(this).data('id');
            $('#startSessionComputerId').val(computerId);
            $('#startSessionModal').modal('show');
        }
    });
    $('.pc-card.digunakan .btn-warning').on('click', function(e) {
        e.stopPropagation();
        const sessionId = $(this).data('sessionid');
        $('#addDurationSessionId').val(sessionId);
        $('#addDurationModal').modal('show');
    });
    $('.pc-card.digunakan .btn-info').on('click', function(e) {
        e.stopPropagation();
        const sessionId = $(this).data('sessionid');
        $('#freezeSessionId').val(sessionId);
        $('#freezeTimeModal').modal('show');
    });
    $('.tombolLanjutkanSesi').on('click', function() {
        const frozenId = $(this).data('frozenid');
        const customerName = $(this).data('customername');
        const remainingTime = $(this).data('time');
        $('#resumeSessionFrozenId').val(frozenId);
        $('#resumeSessionCustomerName').text(customerName);
        $('#resumeSessionRemainingTime').text(remainingTime);
        $('#resumeSessionModal').modal('show');
    });
    $('.pc-card.digunakan .btn-danger[title="Hentikan Sesi"]').on('click', function(e) {
        e.stopPropagation();
        const sessionId = $(this).data('sessionid');
        if (confirm('Yakin ingin menghentikan sesi ini?')) {
            if (sessionId && pcActivityLogs[sessionId]) {
                delete pcActivityLogs[sessionId];
            }
            $('#stopSessionForm input[name="session_id"]').val(sessionId);
            $('#stopSessionForm').submit();
        }
    });
    $('.tombolUbahComputer').on('click', function() {
        const id = $(this).data('id');
        const nomorpc = $(this).data('nomorpc');
        const status = $(this).data('status');
        $('#formUbahComputer').attr('action', BASEURL + '/index.php?url=computers/ubah/' + id);
        $('#ubah_nomor_pc').val(nomorpc);
        $('#ubah_status').val(status);
        $('#ubahComputerModal').modal('show');
    });
    $('.tombolUbahProduk').on('click', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const harga = $(this).data('harga');
        const stok = $(this).data('stok');
        $('#formUbahProduk').attr('action', BASEURL + '/index.php?url=products/ubah/' + id);
        $('#ubah_nama_produk').val(nama);
        $('#ubah_harga').val(harga);
        $('#ubah_stok').val(stok);
        $('#ubah_nama_produk, #ubah_harga, #ubah_stok, #ubah_foto').removeClass('is-invalid');
        $('#ubahProdukModal .invalid-feedback').remove();
        $('#ubahProdukModal').modal('show');
    });
    $('.tombolLihatAktivitas').on('click', function(e) {
        e.stopPropagation();
        const pcCard = $(this).closest('.pc-card');
        const pcNomor = pcCard.data('pc-nomor');
        const sessionId = pcCard.data('sessionid');
        $('#logPcNomor').text(pcNomor);
        $('#logContent').html('');
        $('#tombolMatikanPaksa').data('session-id', sessionId);
        const logs = pcActivityLogs[sessionId] || [];
        if (logs.length === 0) {
            $('#logContent').html('<p class="text-muted text-center fst-italic">Belum ada aktivitas tercatat.</p>');
        } else {
            logs.forEach(log => {
                $('#logContent').append(`<p class="${log.logClass}">[${log.time}] Mengunjungi: ${log.site}</p>`);
            });
        }
    });
    $('#activityLogModal').on('hidden.bs.modal', function () {
    });
    $('#tombolMatikanPaksa').on('click', function() {
        const sessionId = $(this).data('session-id');
        if (confirm('MATIKAN PC INI PAKSA?')) {
            if (sessionId && pcActivityLogs[sessionId]) {
                delete pcActivityLogs[sessionId];
            }
            $('#stopSessionForm input[name="session_id"]').val(sessionId);
            $('#stopSessionForm').submit();
        }
    });
    $('.tombolTambahProdukSesi').on('click', function(e) {
        e.stopPropagation();
        const sessionId = $(this).data('sessionid');
        $('#addSaleSessionId').val(sessionId);
        $('#product_id').trigger('change');
    });
    $('#product_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const maxStok = selectedOption.data('stok') || 0;
        $('#quantity').attr('max', maxStok);
        if (maxStok > 0) {
            $('#stokHelp').text('Stok tersedia: ' + maxStok);
        } else {
            $('#stokHelp').text('Stok habis!');
        }
        $('#quantity').val(1);
    });
    $('#selectAllCheckbox').on('change', function() {
        $('.rowCheckbox').prop('checked', $(this).prop('checked'));
    });
    $('.rowCheckbox').on('change', function() {
        if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
    });
    $('.modal').on('hidden.bs.modal', function (e) {
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
        if ($(this).attr('id') === 'tambahProdukModal' || $(this).attr('id') === 'ubahProdukModal') {
            $(this).find('form')[0].reset();
        }
    });
    function showWarningToast(pcNomor, site) {
        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
                <div class="toast-header">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong class="me-auto">PERINGATAN KEAMANAN</strong>
                    <small>Baru Saja</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <strong>${pcNomor}</strong> terdeteksi mengunjungi: <strong>${site}</strong>
                </div>
            </div>`;
        $('#notification-container').append(toastHTML);
        const newToast = new bootstrap.Toast(document.getElementById(toastId));
        newToast.show();
    }
    const backgroundMonitorInterval = setInterval(function() {
        $('.pc-card.digunakan').each(function() {
            const pcCard = $(this);
            const sessionId = pcCard.data('sessionid');
            if (!sessionId) return;
            if (!pcActivityLogs[sessionId]) {
                pcActivityLogs[sessionId] = [];
            }
            const isVisit = Math.random() < 0.5;
            if (!isVisit) return;
            const isUnsafe = Math.random() < 0.2;
            const site = isUnsafe ? unsafeSites[Math.floor(Math.random() * unsafeSites.length)] : safeSites[Math.floor(Math.random() * safeSites.length)];
            const time = new Date().toLocaleTimeString();
            const logClass = isUnsafe ? 'log-berbahaya' : '';
            const logEntry = { time, site, logClass };
            pcActivityLogs[sessionId].unshift(logEntry);
            if (pcActivityLogs[sessionId].length > 50) {
                pcActivityLogs[sessionId].pop();
            }
            if (isUnsafe) {
                const pcNomor = pcCard.data('pc-nomor');
                const alertIcon = pcCard.find('.pc-alert-icon');
                if (alertIcon.hasClass('d-none')) {
                    alertIcon.removeClass('d-none');
                    showWarningToast(pcNomor, site);
                }
            }
        });
    }, 4000);

    $('.pc-card.tersedia .tombolReportIssue').on('click', function(e) {
        e.stopPropagation();
        const computerId = $(this).data('id');
        if (confirm('Yakin ingin melaporkan PC #' + computerId + ' rusak?')) {
            $('#reportIssueForm').attr('action', BASEURL + '/index.php?url=home/reportIssue/' + computerId);
            $('#reportIssueForm').submit();
        }
    });

    $('.pc-card.rusak_dilaporkan .tombolStartRepair').on('click', function(e) {
        e.stopPropagation();
        const computerId = $(this).data('id');
        $('#startRepairForm').attr('action', BASEURL + '/index.php?url=home/startRepair/' + computerId);
        $('#startRepairForm').submit();
    });

    $('.pc-card.maintenance .tombolFinishRepair').on('click', function(e) {
        e.stopPropagation();
        const computerId = $(this).data('id');
        $('#finishRepairForm').attr('action', BASEURL + '/index.php?url=home/finishRepair/' + computerId);
        $('#finishRepairForm').submit();
    });
});