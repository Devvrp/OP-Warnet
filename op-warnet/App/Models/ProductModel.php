<?php
class ProductModel {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }
    public function getAllProducts(string $search = '', string $sortBy = 'nama_produk', string $sortDir = 'ASC', int $page = 1, int $perPage = 5): array {
        $offset = ($page - 1) * $perPage;
        $allowedSort = ['nama_produk', 'harga', 'stok'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'nama_produk';
        $sortDir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM products
                WHERE deleted_at IS NULL AND nama_produk LIKE CONCAT('%', :search, '%')
                ORDER BY {$sortBy} {$sortDir}
                LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':search', $search);
        $this->db->bind(':limit', $perPage, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    public function countAllProducts(string $search = ''): int {
        $sql = "SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL AND nama_produk LIKE CONCAT('%', :search, '%')";
        $this->db->query($sql);
        $this->db->bind(':search', $search);
        $result = $this->db->single();
        return (int)($result['total'] ?? 0);
    }
    public function findProductById(int $id): ?array {
         $this->db->query("SELECT * FROM products WHERE id = :id AND deleted_at IS NULL");
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }
    private function uploadFoto() {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return 'default.jpg';
        }
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        $namaFile = $_FILES['foto']['name'];
        $ukuranFile = $_FILES['foto']['size'];
        $tmpName = $_FILES['foto']['tmp_name'];
        $ekstensiValid = ['jpg', 'jpeg', 'png'];
        $ekstensiGambar = explode('.', $namaFile);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        if (!in_array($ekstensiGambar, $ekstensiValid)) { return false; }
        if ($ukuranFile > 2000000) { return false; }
        $namaFileBaru = uniqid() . '.' . $ekstensiGambar;
        if (!move_uploaded_file($tmpName, 'img/' . $namaFileBaru)) {
            return false;
        }
        return $namaFileBaru;
    }
    public function tambahDataProduk(array $data, array $files): array {
        $foto = $this->uploadFoto();
        if ($foto === false) {
            return ['success' => false, 'errors' => ['foto' => 'Gagal mengupload foto. Pastikan format (jpg, png) dan ukuran (< 2MB) sesuai.']];
        }
        $query = "INSERT INTO products (nama_produk, harga, stok, foto) VALUES (:nama, :harga, :stok, :foto)";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_produk']);
        $this->db->bind('harga', (int)$data['harga']);
        $this->db->bind('stok', (int)$data['stok']);
        $this->db->bind('foto', $foto);
        $this->db->execute();
        return ['success' => $this->db->rowCount() > 0, 'errors' => []];
    }
    public function ubahDataProduk(int $id, array $data, array $files): array {
        $this->db->query('SELECT foto FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        $db_data = $this->db->single();
        if(!$db_data) return ['success' => false, 'errors' => ['umum' => 'Produk tidak ditemukan.']];
        $fotoLama = $db_data['foto'];
        $foto = $fotoLama;
        if (isset($files['foto']) && $files['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $this->uploadFoto();
            if ($foto === false) {
                return ['success' => false, 'errors' => ['foto' => 'Gagal mengupload foto baru.']];
            }
            if ($fotoLama != 'default.jpg' && $foto != $fotoLama && file_exists('img/' . $fotoLama)) {
                unlink('img/' . $fotoLama);
            }
        } elseif (isset($files['foto']) && $files['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'errors' => ['foto' => 'Terjadi error saat upload foto.']];
        }
        $query = "UPDATE products SET nama_produk = :nama, harga = :harga, stok = :stok, foto = :foto WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('nama', $data['nama_produk']);
        $this->db->bind('harga', (int)$data['harga']);
        $this->db->bind('stok', (int)$data['stok']);
        $this->db->bind('foto', $foto);
        $this->db->bind('id', $id);
        $this->db->execute();
        return ['success' => true, 'errors' => []];
    }
    public function hapusDataProduk(int $id): bool {
        $query = "UPDATE products SET deleted_at = NOW() WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    public function getDeletedProducts(): array {
        $this->db->query('SELECT * FROM products WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC');
        return $this->db->resultSet();
    }
    public function restoreProduct(int $id): bool {
        $query = "UPDATE products SET deleted_at = NULL WHERE id = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    public function forceDeleteProduct(int $id): bool {
        $this->db->query('SELECT foto FROM products WHERE id = :id');
        $this->db->bind('id', $id);
        $row = $this->db->single();
        if ($row && $row['foto'] != 'default.jpg' && file_exists('img/' . $row['foto'])) {
            unlink('img/' . $row['foto']);
        }
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
    public function autoDeleteOld(int $days = 30): int {
        $this->db->query("SELECT id, foto FROM products WHERE deleted_at IS NOT NULL AND deleted_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
        $this->db->bind(':days', $days, PDO::PARAM_INT);
        $rowsToDelete = $this->db->resultSet();
        $deletedCount = 0;
        if (!empty($rowsToDelete)) {
            $idsToDelete = [];
            foreach ($rowsToDelete as $row) {
                $idsToDelete[] = $row['id'];
                if ($row['foto'] != 'default.jpg' && file_exists('img/' . $row['foto'])) {
                    unlink('img/' . $row['foto']);
                }
            }
            if (!empty($idsToDelete)) {
                $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
                $this->db->query("DELETE FROM products WHERE id IN ({$placeholders})");
                foreach ($idsToDelete as $k => $id) {
                    $this->db->bind($k + 1, $id, PDO::PARAM_INT);
                }
                $this->db->execute();
                $deletedCount = $this->db->rowCount();
            }
        }
        return $deletedCount;
    }
    public function restoreBulk(array $ids): int {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->query("UPDATE products SET deleted_at = NULL WHERE id IN ({$placeholders})");
        foreach ($ids as $k => $id) {
            $this->db->bind($k + 1, (int)$id, PDO::PARAM_INT);
        }
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function forceDeleteBulk(array $ids): int {
        if (empty($ids)) return 0;
        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        $this->db->query("SELECT id, foto FROM products WHERE id IN ({$idPlaceholders})");
        foreach ($ids as $k => $id) {
            $this->db->bind($k + 1, (int)$id, PDO::PARAM_INT);
        }
        $rowsToDelete = $this->db->resultSet();
        $deletedCount = 0;
        if (!empty($rowsToDelete)) {
            $actualIdsToDelete = [];
            foreach ($rowsToDelete as $row) {
                $actualIdsToDelete[] = $row['id'];
                if ($row['foto'] != 'default.jpg' && file_exists('img/' . $row['foto'])) {
                    unlink('img/' . $row['foto']);
                }
            }
            if (!empty($actualIdsToDelete)) {
                $deletePlaceholders = implode(',', array_fill(0, count($actualIdsToDelete), '?'));
                $this->db->query("DELETE FROM products WHERE id IN ({$deletePlaceholders})");
                foreach ($actualIdsToDelete as $k => $id) {
                    $this->db->bind($k + 1, $id, PDO::PARAM_INT);
                }
                $this->db->execute();
                $deletedCount = $this->db->rowCount();
            }
        }
        return $deletedCount;
    }
}