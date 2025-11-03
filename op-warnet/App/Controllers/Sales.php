<?php
class Sales extends Controller {
    public function __construct() {
        parent::__construct();
        $this->gate(['admin', 'operator']);
    }

    public function add(): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($sessionId === false || $sessionId === null || $productId === false || $productId === null || $quantity === false || $quantity === null) {
            $this->flash('error', 'Data penjualan tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        $saleModel = $this->model('SaleModel');
        if ($saleModel->recordSale($sessionId, $productId, $quantity)) {
            $this->flash('success', 'Produk berhasil ditambahkan ke sesi.');
        } else {
            $this->flash('error', 'Gagal menambahkan produk (stok mungkin tidak cukup atau produk tidak ditemukan).');
        }
        $this->redirect(BASEURL . '/index.php');
    }

    public function history($sessionId): void {
        $sessionIdInt = filter_var($sessionId, FILTER_VALIDATE_INT);
        if ($sessionIdInt === false || $sessionIdInt === null) {
            $this->flash('error', 'Sesi ID tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        $data['judul'] = 'Riwayat Pembelian Sesi #' . $sessionIdInt;
        $data['sales'] = $this->model('SaleModel')->getSalesForSession($sessionIdInt);
        $data['session_id'] = $sessionIdInt;
        $this->view('Sales/History', $data);
    }
}