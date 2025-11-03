<?php
class Products extends Controller {
    public function __construct() {
        parent::__construct();
        $this->gate(['admin', 'operator']);
    }

    public function index(): void {
        $data['judul'] = 'Manajemen Produk';
        $productModel = $this->model('ProductModel');
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 5;
        $sortBy = $_GET['sort_by'] ?? 'nama_produk';
        $sortDir = $_GET['sort_dir'] ?? 'ASC';
        $data['products'] = $productModel->getAllProducts($q, $sortBy, $sortDir, $page, $perPage);
        $total = $productModel->countAllProducts($q);
        $data['pages'] = (int) ceil($total / $perPage);
        $data['page'] = $page;
        $data['q'] = $q;
        $data['sort_by'] = $sortBy;
        $data['sort_dir'] = $sortDir;
        $this->view('Products/Index', $data);
    }
    public function tambah(): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $validator = new Validator($_POST);
        $validator->required('nama_produk', 'Nama Produk')
                ->minLength('nama_produk', 3)
                ->maxLength('nama_produk', 100)
                ->required('harga', 'Harga')
                ->integer('harga')
                ->min('harga', 0)
                ->required('stok', 'Stok')
                ->integer('stok')
                ->min('stok', 0);
        if ($validator->fails()) {
            $data['judul'] = 'Tambah Produk Gagal';
            $data['errors'] = $validator->getErrors();
            $data['old'] = $_POST;
            $productModel = $this->model('ProductModel');
            $data['products'] = $productModel->getAllProducts();
            $data['pages'] = 1; $data['page'] = 1; $data['q'] = ''; $data['sort_by'] = 'nama_produk'; $data['sort_dir'] = 'ASC';
            $this->view('Products/Index', $data);
        } else {
            $result = $this->model('ProductModel')->tambahDataProduk($_POST, $_FILES);
            if ($result['success']) {
                $this->flash('success', 'Produk berhasil ditambahkan!');
                $this->redirect(BASEURL . '/index.php?url=products');
            } else {
                $this->flash('error', $result['errors']['foto'] ?? 'Gagal menambahkan produk.');
                $data['judul'] = 'Tambah Produk Gagal Upload';
                $data['errors'] = $result['errors'];
                $data['old'] = $_POST;
                $productModel = $this->model('ProductModel');
                $data['products'] = $productModel->getAllProducts();
                $data['pages'] = 1; $data['page'] = 1; $data['q'] = ''; $data['sort_by'] = 'nama_produk'; $data['sort_dir'] = 'ASC';
                $this->view('Products/Index', $data);
            }
        }
    }

    public function hapus($id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        if ($this->model('ProductModel')->hapusDataProduk((int)$id)) {
            $this->flash('success', 'Produk berhasil dipindahkan ke Recycle Bin!');
        } else {
            $this->flash('error', 'Gagal memindahkan produk!');
        }
        $this->redirect(BASEURL . '/index.php?url=products');
    }

    public function ubah($id): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $productId = (int)$id;
        $validator = new Validator($_POST);
        $validator->required('nama_produk', 'Nama Produk')
                ->minLength('nama_produk', 3)
                ->maxLength('nama_produk', 100)
                ->required('harga', 'Harga')
                ->integer('harga')
                ->min('harga', 0)
                ->required('stok', 'Stok')
                ->integer('stok')
                ->min('stok', 0);
        if ($validator->fails()) {
            $data['judul'] = 'Ubah Produk Gagal';
            $data['errors'] = $validator->getErrors();
            $data['row'] = array_merge(['id' => $productId], $_POST);
            $productModel = $this->model('ProductModel');
            $data['products'] = $productModel->getAllProducts();
            $data['pages'] = 1; $data['page'] = 1; $data['q'] = ''; $data['sort_by'] = 'nama_produk'; $data['sort_dir'] = 'ASC';
            $this->view('Products/Index', $data);
        } else {
            $result = $this->model('ProductModel')->ubahDataProduk($productId, $_POST, $_FILES);
            if ($result['success']) {
                $this->flash('success', 'Data produk berhasil diubah!');
                $this->redirect(BASEURL . '/index.php?url=products');
            } else {
                $this->flash('error', $result['errors']['foto'] ?? $result['errors']['umum'] ?? 'Gagal mengubah produk.');
                $data['judul'] = 'Ubah Produk Gagal Upload/Simpan';
                $data['errors'] = $result['errors'];
                $data['row'] = array_merge(['id' => $productId], $_POST);
                $productModel = $this->model('ProductModel');
                $data['products'] = $productModel->getAllProducts();
                $data['pages'] = 1; $data['page'] = 1; $data['q'] = ''; $data['sort_by'] = 'nama_produk'; $data['sort_dir'] = 'ASC';
                $this->view('Products/Index', $data);
            }
        }
    }

    public function recycleBin(): void {
        $this->gate(['admin']);
        $data['judul'] = 'Recycle Bin Produk';
        $data['deleted_products'] = $this->model('ProductModel')->getDeletedProducts();
        $this->view('Products/RecycleBin', $data);
    }

    public function restore($id): void {
        $this->gate(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        if ($this->model('ProductModel')->restoreProduct((int)$id)) {
            $this->flash('success', 'Produk berhasil dipulihkan!');
        } else {
            $this->flash('error', 'Gagal memulihkan produk!');
        }
        $this->redirect(BASEURL . '/index.php?url=products/recycleBin');
    }
    public function forceDelete($id): void {
        $this->gate(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        if ($this->model('ProductModel')->forceDeleteProduct((int)$id)) {
            $this->flash('success', 'Produk berhasil dihapus permanen!');
        } else {
            $this->flash('error', 'Gagal menghapus produk permanen!');
        }
        $this->redirect(BASEURL . '/index.php?url=products/recycleBin');
    }
    public function autoDelete(): void {
        $this->gate(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $deletedCount = $this->model('ProductModel')->autoDeleteOld();
        if ($deletedCount > 0) {
            $this->flash('success', $deletedCount . ' produk lama di Recycle Bin berhasil dihapus permanen.');
        } else {
            $this->flash('info', 'Tidak ada produk lama di Recycle Bin yang perlu dihapus.');
        }
        $this->redirect(BASEURL . '/index.php?url=products/recycleBin');
    }
    public function bulkAction(): void {
        $this->gate(['admin']);
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $action = $_POST['bulk_action'] ?? '';
        $ids = $_POST['ids'] ?? [];
        if (empty($ids) || !is_array($ids)) {
            $this->flash('error', 'Tidak ada produk yang dipilih.');
            $this->redirect(BASEURL . '/index.php?url=products/recycleBin');
            return;
        }
        $productModel = $this->model('ProductModel');
        $processedCount = 0;
        if ($action === 'restore') {
            $processedCount = $productModel->restoreBulk($ids);
            if ($processedCount > 0) {
                $this->flash('success', $processedCount . ' produk berhasil dipulihkan.');
            } else {
                $this->flash('error', 'Gagal memulihkan produk yang dipilih.');
            }
        } elseif ($action === 'forceDelete') {
            $processedCount = $productModel->forceDeleteBulk($ids);
            if ($processedCount > 0) {
                $this->flash('success', $processedCount . ' produk berhasil dihapus permanen.');
            } else {
                $this->flash('error', 'Gagal menghapus permanen produk yang dipilih.');
            }
        } else {
            $this->flash('error', 'Aksi tidak valid.');
        }
        $this->redirect(BASEURL . '/index.php?url=products/recycleBin');
    }
}