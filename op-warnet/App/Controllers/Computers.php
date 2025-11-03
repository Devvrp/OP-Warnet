<?php
class Computers extends Controller {
    public function __construct() {
        parent::__construct();
        $this->gate(['admin', 'maintenance']);
    }

    public function index(): void {
        $data['judul'] = 'Manajemen Komputer';
        $data['computers'] = $this->model('ComputerModel')->getAllComputers();
        $this->view('Computers/Index', $data);
    }

    public function tambah(): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $validator = new Validator($_POST);
        $validator->required('nomor_pc', 'Nomor PC')
                ->maxLength('nomor_pc', 20)
                ->unique('nomor_pc', 'computers', 'nomor_pc', null, 'Nomor PC');
        if ($validator->fails()) {
            $this->flash('error', implode(', ', array_merge(...array_values($validator->getErrors()))));
            $this->redirect(BASEURL . '/index.php?url=computers');
        } else {
            if ($this->model('ComputerModel')->tambahDataComputer($_POST) > 0) {
                $this->flash('success', 'Komputer berhasil ditambahkan.');
            } else {
                $this->flash('error', 'Gagal menambahkan komputer.');
            }
            $this->redirect(BASEURL . '/index.php?url=computers');
        }
    }

    public function hapus($id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        if ($this->model('ComputerModel')->hapusDataComputer((int)$id) > 0) {
            $this->flash('success', 'Komputer berhasil dihapus.');
        } else {
            $this->flash('error', 'Gagal menghapus komputer.');
        }
        $this->redirect(BASEURL . '/index.php?url=computers');
    }

    public function ubah($id): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $computerId = (int)$id;
        $validator = new Validator($_POST);
        $validator->required('nomor_pc', 'Nomor PC')
                ->maxLength('nomor_pc', 20)
                ->unique('nomor_pc', 'computers', 'nomor_pc', $computerId, 'Nomor PC')
                ->required('status', 'Status')
                ->in('status', ['tersedia', 'digunakan', 'maintenance'], 'Status');
        if ($validator->fails()) {
            $this->flash('error', implode(', ', array_merge(...array_values($validator->getErrors()))));
            $this->redirect(BASEURL . '/index.php?url=computers');
        } else {
            $_POST['id'] = $computerId;
            if ($this->model('ComputerModel')->ubahDataComputer($_POST) > 0) {
                $this->flash('success', 'Data komputer berhasil diubah.');
            } else {
                $this->flash('info', 'Tidak ada perubahan data komputer.');
            }
            $this->redirect(BASEURL . '/index.php?url=computers');
        }
    }
}