<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><i class="bi bi-receipt me-2"></i>Riwayat Pembelian Sesi #<?= $data['session_id']; ?></h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASEURL; ?>/index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Waktu</th>
                    <th scope="col">Nama Produk</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Harga Satuan</th>
                    <th scope="col">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['sales'])): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted fst-italic py-3">Belum ada pembelian produk untuk sesi ini.</td>
                    </tr>
                <?php else: ?>
                    <?php $i=1; foreach($data['sales'] as $sale): ?>
                    <tr>
                        <th scope="row"><?= $i++; ?></th>
                        <td><?= date('d M Y H:i:s', strtotime($sale['sale_time'])); ?></td>
                        <td><?= htmlspecialchars($sale['nama_produk']); ?></td>
                        <td><?= htmlspecialchars($sale['quantity']); ?></td>
                        <td>Rp <?= number_format($sale['price_per_item']); ?></td>
                        <td>Rp <?= number_format($sale['total_price']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>