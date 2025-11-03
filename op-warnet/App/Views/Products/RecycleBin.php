<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><i class="bi bi-trash3 me-2"></i>Recycle Bin Produk</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASEURL; ?>/index.php?url=products" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Daftar Produk
            </a>
            <form action="<?= BASEURL; ?>/index.php?url=products/autoDelete" method="post" class="d-inline">
                <?= Csrf::input(); ?>
                <button type="submit" class="btn btn-warning" onclick="return confirm('Hapus permanen semua produk di Recycle Bin yang lebih tua dari 30 hari?')">
                    <i class="bi bi-eraser me-1"></i> Bersihkan Otomatis
                </button>
            </form>
        </div>
    </div>
    <form action="<?= BASEURL; ?>/index.php?url=products/bulkAction" method="post" id="bulkActionForm">
        <?= Csrf::input(); ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5%;"><input type="checkbox" id="selectAllCheckbox"></th>
                        <th scope="col" style="width: 15%;">Foto</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col" style="width: 15%;">Harga</th>
                        <th scope="col" style="width: 10%;">Stok</th>
                        <th scope="col" style="width: 15%;">Tgl Dihapus</th>
                        <th scope="col" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['deleted_products'])): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted fst-italic py-3">Recycle Bin kosong.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['deleted_products'] as $p): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $p['id']; ?>" class="rowCheckbox"></td>
                            <td><img src="<?= BASEURL . '/img/' . htmlspecialchars($p['foto']); ?>" alt="Foto <?= htmlspecialchars($p['nama_produk']); ?>" class="img-thumbnail"></td>
                            <td><?= htmlspecialchars($p['nama_produk']); ?></td>
                            <td>Rp <?= number_format($p['harga']); ?></td>
                            <td><?= htmlspecialchars($p['stok']); ?></td>
                            <td><?= DateHelper::format($p['deleted_at'], 'd M Y H:i'); ?></td>
                            <td>
                                <form action="<?= BASEURL; ?>/index.php?url=products/restore/<?= $p['id']; ?>" method="post" class="d-inline">
                                    <?= Csrf::input(); ?>
                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Pulihkan produk <?= htmlspecialchars($p['nama_produk']); ?>?')"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>
                                </form>
                                <form action="<?= BASEURL; ?>/index.php?url=products/forceDelete/<?= $p['id']; ?>" method="post" class="d-inline">
                                    <?= Csrf::input(); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('HAPUS PERMANEN produk <?= htmlspecialchars($p['nama_produk']); ?>? Aksi ini tidak bisa dibatalkan.')"><i class="bi bi-x-octagon me-1"></i>Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($data['deleted_products'])): ?>
        <div class="mt-3">
            <select name="bulk_action" class="form-select d-inline-block" style="width: auto;">
                <option value="">-- Pilih Aksi Bulk --</option>
                <option value="restore">Restore Terpilih</option>
                <option value="forceDelete">Hapus Permanen Terpilih</option>
            </select>
            <button type="submit" class="btn btn-primary ms-2" onclick="return confirm('Lakukan aksi bulk pada item terpilih?')">Terapkan</button>
        </div>
        <?php endif; ?>
    </form>
</div>