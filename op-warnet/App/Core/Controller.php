<?php
class Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function view(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $flashes = $this->takeFlashes();
        $base = BASEURL;
        $active = explode('/', $_GET['url'] ?? 'home/index')[0];
        
        require_once __DIR__ . '/../Views/Templates/Header.php';
        require_once __DIR__ . '/../Views/' . $view . '.php';
        require_once __DIR__ . '/../Views/Templates/Footer.php';
    }

    public function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $flashes = $this->takeFlashes();
        $base = BASEURL;
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    public function model(string $model): object {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model;
    }

    public function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    public function flash(string $type, string $message): void {
        $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
    }

    protected function takeFlashes(): array {
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);
        return $flashes;
    }

    protected function gate(array $allowedRoles = []): void {
        if (!isset($_SESSION['user_id'])) {
            $this->flash('error', 'Anda harus login untuk mengakses halaman ini.');
            $this->redirect(BASEURL . '/index.php?url=auth');
        }
        
        $userRole = $_SESSION['user_role'] ?? null;
        if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
            $this->flash('error', 'Anda tidak memiliki hak akses ke halaman ini.');
            $this->redirect(BASEURL . '/index.php');
        }
    }

    protected function isGuest(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(BASEURL . '/index.php');
        }
    }
}