<div class="container mt-4">
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div id="notification-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <h4><i class="bi bi-display"></i> Dashboard Komputer</h4>
            <hr>
            <div class="row">
                <?php $role = $_SESSION['user_role'] ?? null; ?>
                <?php foreach($data['computers'] as $pc) : ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card pc-card shadow-sm <?= $pc['status']; ?>" 
                            data-id="<?= $pc['id']; ?>" 
                            <?= ($pc['status'] == 'digunakan' && isset($pc['session_id'])) ? 'data-sessionid="' . $pc['session_id'] . '"' : ''; ?>
                            data-pc-nomor="<?= $pc['nomor_pc']; ?>">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">
                                    <?= $pc['nomor_pc']; ?> 
                                    <i class="bi bi-exclamation-triangle-fill text-danger pc-alert-icon d-none" title="Aktivitas mencurigakan terdeteksi!"></i>
                                </h5>
                                <?php if($pc['status'] == 'tersedia'): ?>
                                    <p class="card-text text-success fw-bold">Tersedia</p>
                                    <?php if ($role === 'admin' || $role === 'operator'): ?>
                                    <small class="text-muted">Klik untuk memulai sesi</small>
                                    <?php else: ?>
                                    <small class="text-muted">Status: Tersedia</small>
                                    <?php endif; ?>
                                <?php elseif($pc['status'] == 'digunakan'): ?>
                                    <p class="card-text text-warning-emphasis fw-bold">Digunakan</p>
                                    <div class="countdown-timer" data-endtime="<?= $pc['end_time_unix']; ?>"></div>
                                    <?php if ($role === 'admin' || $role === 'operator'): ?>
                                    <div class="mt-2 d-flex justify-content-start gap-1 flex-wrap">
                                        <button class="btn btn-warning btn-sm" title="Tambah Durasi" data-bs-toggle="modal" data-bs-target="#addDurationModal" data-sessionid="<?= $pc['session_id']; ?>"><i class="bi bi-plus-circle"></i></button>
                                        <button class="btn btn-info btn-sm text-white" title="Bekukan Sesi" data-bs-toggle="modal" data-bs-target="#freezeTimeModal" data-sessionid="<?= $pc['session_id']; ?>"><i class="bi bi-snow"></i></button>
                                        <button class="btn btn-primary btn-sm tombolTambahProdukSesi" title="Tambah Produk" data-bs-toggle="modal" data-bs-target="#addSaleModal" data-session-id="<?= $pc['session_id']; ?>"><i class="bi bi-cart-plus"></i></button>
                                        <a href="<?= BASEURL; ?>/index.php?url=sales/history/<?= $pc['session_id']; ?>" class="btn btn-success btn-sm" title="Riwayat Pembelian"><i class="bi bi-receipt"></i></a>
                                        <button class="btn btn-secondary btn-sm tombolLihatAktivitas" title="Lihat Aktivitas" data-bs-toggle="modal" data-bs-target="#activityLogModal"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-danger btn-sm" title="Hentikan Sesi" data-sessionid="<?= $pc['session_id']; ?>"><i class="bi bi-power"></i></button>
                                    </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="card-text text-secondary fw-bold">Maintenance</p>
                                    <small class="text-muted">Tidak dapat digunakan</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <h4><i class="bi bi-person-bounding-box"></i> Sesi Dibekukan</h4>
            <hr>
            <?php if(empty($data['frozen_sessions'])): ?>
                <div class="alert alert-info">Belum ada sesi yang dibekukan.</div>
            <?php else: ?>
                <ul class="list-group">
                <?php foreach($data['frozen_sessions'] as $fs): ?>
                    <?php
                        $hours = floor($fs['remaining_seconds'] / 3600);
                        $minutes = floor(($fs['remaining_seconds'] % 3600) / 60);
                        $time_str = sprintf('%02d jam %02d mnt', $hours, $minutes);
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($fs['customer_name']); ?></strong><br>
                            <small>Sisa Waktu: <?= $time_str; ?></small>
                        </div>
                        <?php if ($role === 'admin' || $role === 'operator'): ?>
                        <button class="btn btn-success btn-sm tombolLanjutkanSesi" data-bs-toggle="modal" data-bs-target="#resumeSessionModal" data-frozenid="<?= $fs['id']; ?>" data-customername="<?= htmlspecialchars($fs['customer_name']); ?>" data-time="<?= $time_str; ?>">Lanjutkan</button>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<form id="stopSessionForm" action="<?= BASEURL; ?>/index.php?url=home/stopSession" method="post" style="display: none;">
    <?= Csrf::input(); ?>
    <input type="hidden" name="session_id" value="">
</form>
<div class="modal fade" id="startSessionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Mulai Sesi Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/index.php?url=home/startSession" method="post">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <input type="hidden" name="computer_id" id="startSessionComputerId">
            <div class="mb-3">
                <label for="nominal" class="form-label">Masukkan Nominal (Tarif: Rp <?= number_format($data['rate'] ?? 5000); ?>/jam)</label>
                <input type="number" class="form-control" id="nominal" name="nominal" required min="1000" step="1000">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Mulai</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="addDurationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Durasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/index.php?url=home/addDuration" method="post">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <input type="hidden" name="session_id" id="addDurationSessionId">
            <div class="mb-3">
                <label for="nominal_add" class="form-label">Masukkan Nominal Tambahan</label>
                <input type="number" class="form-control" id="nominal_add" name="nominal" required min="1000" step="1000">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="freezeTimeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bekukan Waktu Sesi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/index.php?url=home/freezeSession" method="post">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <input type="hidden" name="session_id" id="freezeSessionId">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Nama Pelanggan</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <p class="form-text">Sisa waktu akan disimpan atas nama ini.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info text-white">Bekukan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="resumeSessionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lanjutkan Sesi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/index.php?url=home/resumeSession" method="post">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <input type="hidden" name="frozen_id" id="resumeSessionFrozenId">
            <p>Lanjutkan sesi untuk <strong><span id="resumeSessionCustomerName"></span></strong>?</p>
            <p>Sisa Waktu: <strong id="resumeSessionRemainingTime"></strong></p>
            <div class="mb-3">
                <label for="computer_id_resume" class="form-label">Pilih PC yang Tersedia</label>
                <select name="computer_id" id="computer_id_resume" class="form-select" required>
                    <option value="">-- Pilih PC --</option>
                    <?php foreach($data['computers'] as $pc): ?>
                        <?php if($pc['status'] == 'tersedia'): ?>
                            <option value="<?= $pc['id']; ?>"><?= $pc['nomor_pc']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Lanjutkan Sesi</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="activityLogModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Log Aktivitas - <span id="logPcNomor"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="logContent" style="min-height: 300px; max-height: 400px; overflow-y: auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="tombolMatikanPaksa">Matikan PC Paksa</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addSaleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Pembelian Produk ke Sesi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/index.php?url=sales/add" method="post">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <input type="hidden" name="session_id" id="addSaleSessionId">
            <div class="mb-3">
                <label for="product_id" class="form-label">Pilih Produk</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach($data['products_for_sale'] as $prod): ?>
                        <?php if($prod['stok'] > 0) : ?>
                            <option value="<?= $prod['id']; ?>" data-stok="<?= $prod['stok']; ?>">
                                <?= htmlspecialchars($prod['nama_produk']); ?> (Stok: <?= $prod['stok']; ?>) - Rp <?= number_format($prod['harga']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1" value="1">
                <small id="stokHelp" class="form-text text-muted"></small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah Pembelian</button>
        </div>
      </form>
    </div>
  </div>
</div>