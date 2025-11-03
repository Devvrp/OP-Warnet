<?php
require 'App/Init.php';
require 'App/Database/Factories/ProductFactory.php';

try {
    $db = new Database();
    echo "Koneksi database berhasil.<br>";
    $db->query("SET FOREIGN_KEY_CHECKS = 0;");
    $db->execute();
    $db->query("TRUNCATE TABLE users");
    $db->execute();
    echo "Tabel 'users' dibersihkan.<br>";
    $users = [
        [
            'nama' => 'Admin Utama',
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'role' => 'admin'
        ],
        [
            'nama' => 'Operator Sesi',
            'username' => 'operator',
            'password' => password_hash('operator123', PASSWORD_BCRYPT),
            'role' => 'operator'
        ],
        [
            'nama' => 'Tim Maintenance',
            'username' => 'maintenance',
            'password' => password_hash('mtc123', PASSWORD_BCRYPT),
            'role' => 'maintenance'
        ],
    ];
    $stmt = "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, :role)";
    foreach ($users as $user) {
        $db->query($stmt);
        $db->bind(':nama', $user['nama']);
        $db->bind(':username', $user['username']);
        $db->bind(':password', $user['password']);
        $db->bind(':role', $user['role']);
        $db->execute();
    }
    echo "Berhasil seeding 3 data users (dengan HASH BARU).<br>";
    $db->query("TRUNCATE TABLE products");
    $db->execute();
    $db->query("TRUNCATE TABLE billing_sessions");
    $db->execute();
    $db->query("TRUNCATE TABLE sales");
    $db->execute();
    $db->query("TRUNCATE TABLE frozen_sessions");
    $db->execute();
    $db->query("UPDATE computers SET status = 'tersedia', current_session_id = NULL");
    $db->execute();
    echo "Tabel 'products', 'billing_sessions', 'sales', 'frozen_sessions' dibersihkan dan PC direset.<br>";
    $factory = new ProductFactory();
    $factory->create(20);
    echo "Berhasil seeding 20 data produk.<br>";
    $db->query("SET FOREIGN_KEY_CHECKS = 1;");
    $db->execute();
    echo "<hr><strong>Seeding selesai. Database siap digunakan.</strong>";
} catch (PDOException $e) {
    die("Seeding gagal: " . $e->getMessage());
}