<?php
class Auth extends Controller {
    public function __construct() {
        parent::__construct();
        $this->isGuest();
    }
    public function index(): void {
        Csrf::clearToken();
        $data['judul'] = 'Login';
        $this->render('Auth/Login', $data);
    }
    public function login(): void {
        Csrf::verifyOrFail($_POST['_csrf_token'] ?? null);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $validatorData = ['username' => $username, 'password' => $password];
        $validator = new Validator($validatorData);
        $validator->required('username', 'Username')
                ->required('password', 'Password');
        if ($validator->fails()) {
            $this->flash('error', 'Username dan Password wajib diisi.');
            $this->redirect(BASEURL . '/index.php?url=auth');
            return;
        }
        $userModel = $this->model('UserModel');
        $user = $userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            
            $this->flash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama']) . '!');
            $this->redirect(BASEURL . '/index.php');
        } else {
            $this->flash('error', 'Username atau Password salah.');
            $this->redirect(BASEURL . '/index.php?url=auth');
        }
    }
}