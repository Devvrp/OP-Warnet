<?php
class Home extends Controller {
    public function __construct() {
        parent::__construct();
        $this->gate(['admin', 'operator', 'maintenance']);
    }

    public function index(): void {
        $data['judul'] = 'Dashboard PC';
        $computer_model = $this->model('ComputerModel');
        $data['computers'] = $computer_model->getAllComputerSessions();
        $data['frozen_sessions'] = $computer_model->getAllFrozenSessions();
        $settingModel = $this->model('SettingModel');
        $data['rate'] = $settingModel->getSetting('billing_rate_per_hour');
        $data['products_for_sale'] = $this->model('ProductModel')->getAllProducts('', 'nama_produk', 'ASC', 1, 1000);
        $this->view('Home/Index', $data);
    }

    public function logout(): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        session_unset();
        session_destroy();
        $this->redirect(BASEURL . '/index.php?url=auth');
    }

    public function startSession(): void {
        $this->gate(['admin', 'operator']);
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $rate = $this->model('SettingModel')->getSetting('billing_rate_per_hour');
        $nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1000]]);
        $computerId = filter_input(INPUT_POST, 'computer_id', FILTER_VALIDATE_INT);
        if ($nominal === false || $computerId === false || $computerId === null) {
            $this->flash('error', 'Input tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        $durationInSeconds = floor(((int)$nominal / (int)$rate) * 3600);
        $_POST['duration'] = $durationInSeconds;
        if ($this->model('ComputerModel')->startNewSession($_POST) > 0) {
            $this->flash('success', 'Sesi berhasil dimulai.');
        } else {
            $this->flash('error', 'Gagal memulai sesi.');
        }
        $this->redirect(BASEURL . '/index.php');
    }

    public function stopSession(): void {
        $this->gate(['admin', 'operator']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
        if ($sessionId === false || $sessionId === null) {
            $this->flash('error', 'Sesi ID tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        if ($this->model('ComputerModel')->endSession($sessionId) > 0) {
            $this->flash('success', 'Sesi berhasil dihentikan.');
        } else {
            $this->flash('error', 'Gagal menghentikan sesi.');
        }
        $this->redirect(BASEURL . '/index.php');
    }

    public function addDuration(): void {
        $this->gate(['admin', 'operator']);
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $rate = $this->model('SettingModel')->getSetting('billing_rate_per_hour');
        $nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1000]]);
        $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
        if ($nominal === false || $sessionId === false || $sessionId === null) {
            $this->flash('error', 'Input tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        $additionalSeconds = floor(((int)$nominal / (int)$rate) * 3600);
        $_POST['duration'] = $additionalSeconds;
        if ($this->model('ComputerModel')->addDurationToSession($_POST) > 0) {
            $this->flash('success', 'Durasi berhasil ditambahkan.');
        } else {
            $this->flash('error', 'Gagal menambahkan durasi.');
        }
        $this->redirect(BASEURL . '/index.php');
    }

    public function freezeSession(): void {
        $this->gate(['admin', 'operator']);
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
        $customerName = trim($_POST['customer_name'] ?? '');
        if ($sessionId === false || $sessionId === null || empty($customerName)) {
            $this->flash('error', 'Input tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        if ($this->model('ComputerModel')->freezeSession($_POST) > 0) {
            $this->flash('success', 'Sesi berhasil dibekukan untuk ' . htmlspecialchars($customerName) . '.');
        } else {
            $this->flash('error', 'Gagal membekukan sesi.');
        }
        $this->redirect(BASEURL . '/index.php');
    }

    public function resumeSession(): void {
        $this->gate(['admin', 'operator']);
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $frozenId = filter_input(INPUT_POST, 'frozen_id', FILTER_VALIDATE_INT);
        $computerId = filter_input(INPUT_POST, 'computer_id', FILTER_VALIDATE_INT);
        if ($frozenId === false || $frozenId === null || $computerId === false || $computerId === null) {
            $this->flash('error', 'Input tidak valid.');
            $this->redirect(BASEURL . '/index.php');
            return;
        }
        if ($this->model('ComputerModel')->resumeFrozenSession($_POST) > 0) {
            $this->flash('success', 'Sesi berhasil dilanjutkan.');
        } else {
            $this->flash('error', 'Gagal melanjutkan sesi.');
        }
        $this->redirect(BASEURL . '/index.php');
    }
}