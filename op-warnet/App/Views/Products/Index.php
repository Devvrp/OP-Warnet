<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3><i class="bi bi-cart3 me-2"></i>Manajemen Produk</h3>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProdukModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Produk
            </button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6 offset-md-6">
            <form action="<?= BASEURL; ?>/index.php" method="GET">
                <input type="hidden" name="url" value="products/index">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari nama produk..." name="q" value="<?= htmlspecialchars($data['q']); ?>">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 5%;">#</th>
                    <th scope="col" style="width: 15%;">Foto</th>
                    <th>
                        <a href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=nama_produk&sort_dir=<?= ($data['sort_by'] == 'nama_produk' && $data['sort_dir'] == 'ASC') ? 'DESC' : 'ASC'; ?>" class="text-white text-decoration-none">
                            Nama Produk <?= ($data['sort_by'] == 'nama_produk') ? ($data['sort_dir'] == 'ASC' ? '<i class="bi bi-sort-alpha-down"></i>' : '<i class="bi bi-sort-alpha-up-alt"></i>') : ''; ?>
                        </a>
                    </th>
                    <th style="width: 15%;">
                        <a href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=harga&sort_dir=<?= ($data['sort_by'] == 'harga' && $data['sort_dir'] == 'ASC') ? 'DESC' : 'ASC'; ?>" class="text-white text-decoration-none">
                            Harga <?= ($data['sort_by'] == 'harga') ? ($data['sort_dir'] == 'ASC' ? '<i class="bi bi-sort-numeric-down"></i>' : '<i class="bi bi-sort-numeric-up-alt"></i>') : ''; ?>
                        </a>
                    </th>
                    <th style="width: 10%;">
                        <a href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=stok&sort_dir=<?= ($data['sort_by'] == 'stok' && $data['sort_dir'] == 'ASC') ? 'DESC' : 'ASC'; ?>" class="text-white text-decoration-none">
                            Stok <?= ($data['sort_by'] == 'stok') ? ($data['sort_dir'] == 'ASC' ? '<i class="bi bi-sort-numeric-down"></i>' : '<i class="bi bi-sort-numeric-up-alt"></i>') : ''; ?>
                        </a>
                    </th>
                    <th scope="col" style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['products'])): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted fst-italic py-3">
                            <?= empty($data['q']) ? 'Belum ada produk.' : 'Produk tidak ditemukan.'; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $i = (($data['page'] - 1) * 5) + 1; foreach($data['products'] as $p): ?>
                    <tr>
                        <th scope="row"><?= $i++; ?></th>
                        <td><img src="<?= BASEURL . '/img/' . htmlspecialchars($p['foto']); ?>" alt="Foto <?= htmlspecialchars($p['nama_produk']); ?>" class="img-thumbnail"></td>
                        <td><?= htmlspecialchars($p['nama_produk']); ?></td>
                        <td>Rp <?= number_format($p['harga']); ?></td>
                        <td><?= htmlspecialchars($p['stok']); ?></td>
                        <td>
                            <button class="btn btn-success btn-sm tombolUbahProduk" data-bs-toggle="modal" data-bs-target="#ubahProdukModal" data-id="<?= $p['id']; ?>" data-nama="<?= htmlspecialchars($p['nama_produk']); ?>" data-harga="<?= $p['harga']; ?>" data-stok="<?= $p['stok']; ?>"><i class="bi bi-pencil-square me-1"></i>Ubah</button>
                            <form action="<?= BASEURL; ?>/index.php?url=products/hapus/<?= $p['id']; ?>" method="post" class="d-inline">
                                <?= Csrf::input(); ?>
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Pindahkan produk <?= htmlspecialchars($p['nama_produk']); ?> ke Recycle Bin?')"><i class="bi bi-trash me-1"></i>Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($data['pages'] > 1): ?>
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
            <li class="page-item <?= ($data['page'] <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=<?= $data['sort_by'] ?>&sort_dir=<?= $data['sort_dir'] ?>&page=<?= $data['page'] - 1; ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $data['pages']; $i++): ?>
                <li class="page-item <?= ($i == $data['page']) ? 'active' : ''; ?>">
                    <a class="page-link" href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=<?= $data['sort_by'] ?>&sort_dir=<?= $data['sort_dir'] ?>&page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($data['page'] >= $data['pages']) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?url=products/index&q=<?= urlencode($data['q']) ?>&sort_by=<?= $data['sort_by'] ?>&sort_dir=<?= $data['sort_dir'] ?>&page=<?= $data['page'] + 1; ?>">Next</a>
            </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<div class="modal fade" id="tambahProdukModal" tabindex="-1" aria-labelledby="tambahProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="tambahProdukModalLabel">Tambah Produk Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= BASEURL; ?>/index.php?url=products/tambah" method="post" enctype="multipart/form-data">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control <?= isset($data['errors']['nama_produk']) ? 'is-invalid' : ''; ?>" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($data['old']['nama_produk'] ?? ''); ?>" required>
                <?php if (isset($data['errors']['nama_produk'])): ?><div class="invalid-feedback"><?= implode('<br>', $data['errors']['nama_produk']); ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control <?= isset($data['errors']['harga']) ? 'is-invalid' : ''; ?>" id="harga" name="harga" value="<?= htmlspecialchars($data['old']['harga'] ?? ''); ?>" required min="0">
                <?php if (isset($data['errors']['harga'])): ?><div class="invalid-feedback"><?= implode('<br>', $data['errors']['harga']); ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control <?= isset($data['errors']['stok']) ? 'is-invalid' : ''; ?>" id="stok" name="stok" value="<?= htmlspecialchars($data['old']['stok'] ?? ''); ?>" required min="0">
                <?php if (isset($data['errors']['stok'])): ?><div class="invalid-feedback"><?= implode('<br>', $data['errors']['stok']); ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto Produk</label>
                <input type="file" class="form-control <?= isset($data['errors']['foto']) ? 'is-invalid' : ''; ?>" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg">
                <?php if (isset($data['errors']['foto'])): ?><div class="invalid-feedback"><?= implode('<br>', $data['errors']['foto']); ?></div><?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
        </form>
    </div>
    </div>
</div>

<div class="modal fade" id="ubahProdukModal" tabindex="-1" aria-labelledby="ubahProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="ubahProdukModalLabel">Ubah Data Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formUbahProduk" method="post" enctype="multipart/form-data">
        <?= Csrf::input(); ?>
        <div class="modal-body">
            <div class="mb-3">
                <label for="ubah_nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="ubah_nama_produk" name="nama_produk" value="" required>
            </div>
            <div class="mb-3">
                <label for="ubah_harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="ubah_harga" name="harga" value="" required min="0">
            </div>
            <div class="mb-3">
                <label for="ubah_stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="ubah_stok" name="stok" value="" required min="0">
            </div>
            <div class="mb-3">
                <label for="ubah_foto" class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" class="form-control" id="ubah_foto" name="foto" accept="image/png, image/jpeg, image/jpg">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </div>
        </form>
    </div>
    </div>
</div>

<?php if (!empty($data['errors'])): ?>
<script>
$(function() {
    <?php if (isset($data['old'])): ?>
        var tambahModal = new bootstrap.Modal(document.getElementById('tambahProdukModal'));
        tambahModal.show();
    <?php elseif (isset($data['row'])): ?>
        var ubahModalEl = document.getElementById('ubahProdukModal');
        var ubahModal = bootstrap.Modal.getInstance(ubahModalEl);
        if (!ubahModal) {
            ubahModal = new bootstrap.Modal(ubahModalEl);
        }
        var currentId = <?= json_encode($data['row']['id'] ?? 0); ?>;
        $('#ubah_nama_produk').val(<?= json_encode($data['row']['nama_produk'] ?? '') ?>);
        $('#ubah_harga').val(<?= json_encode($data['row']['harga'] ?? '') ?>);
        $('#ubah_stok').val(<?= json_encode($data['row']['stok'] ?? '') ?>);
        $('#formUbahProduk').attr('action', BASEURL + '/index.php?url=products/ubah/' + currentId);
        <?php foreach ($data['errors'] as $field => $messages): ?>
            var input = $('#ubah_<?= $field ?>');
            if(input.length === 0 && '<?= $field ?>' === 'nama_produk'){
                input = $('#ubah_nama_produk');
            } else if(input.length === 0 && '<?= $field ?>' === 'harga'){
                input = $('#ubah_harga');
            } else if(input.length === 0 && '<?= $field ?>' === 'stok'){
                input = $('#ubah_stok');
            } else if(input.length === 0 && '<?= $field ?>' === 'foto'){
                input = $('#ubah_foto');
            }
            if(input.length > 0){
                input.addClass('is-invalid');
                var feedbackDiv = input.closest('.mb-3').find('.invalid-feedback');
                if(feedbackDiv.length === 0){
                    input.closest('.mb-3').append('<div class="invalid-feedback"><?= implode('<br>', array_map('htmlspecialchars', $messages)); ?></div>');
                } else {
                    feedbackDiv.html('<?= implode('<br>', array_map('htmlspecialchars', $messages)); ?>');
                }
            }
        <?php endforeach; ?>
        ubahModal.show();
    <?php endif; ?>
});
</script>
<?php endif; ?>