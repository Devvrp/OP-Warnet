<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Manajemen Komputer</h3>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahComputerModal">
                <i class="bi bi-plus-circle"></i> Tambah Komputer
            </button>
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nomor PC</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach($data['computers'] as $pc): ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $pc['nomor_pc']; ?></td>
                <td><?= ucfirst($pc['status']); ?></td>
                <td>
                    <button class="btn btn-success btn-sm tombolUbahComputer" data-id="<?= $pc['id']; ?>" data-nomorpc="<?= $pc['nomor_pc']; ?>" data-status="<?= $pc['status']; ?>">Ubah</button>
                    <a href="<?= BASEURL; ?>/computers/hapus/<?= $pc['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="tambahComputerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Komputer Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASEURL; ?>/computers/tambah" method="post">
        <div class="modal-body">
            <div class="mb-3">
                <label for="nomor_pc" class="form-label">Nomor PC</label>
                <input type="text" class="form-control" id="nomor_pc" name="nomor_pc" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="ubahComputerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ubah Data Komputer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formUbahComputer" method="post">
        <div class="modal-body">
            <div class="mb-3">
                <label for="ubah_nomor_pc" class="form-label">Nomor PC</label>
                <input type="text" class="form-control" id="ubah_nomor_pc" name="nomor_pc" required>
            </div>
            <div class="mb-3">
                <label for="ubah_status" class="form-label">Status</label>
                <select id="ubah_status" name="status" class="form-select">
                    <option value="tersedia">Tersedia</option>
                    <option value="digunakan">Digunakan</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-success">Ubah Data</button>
        </div>
      </form>
    </div>
  </div>
</div>