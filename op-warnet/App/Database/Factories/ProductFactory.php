<?php
class ProductFactory {
    private Database $db;
    public function __construct() {
        $this->db = new Database();
    }
    private function getRandomProductName(): string {
        $adjectives = ['Lezat', 'Nikmat', 'Segar', 'Crispy', 'Manis', 'Gurih', 'Pedas', 'Dingin', 'Panas', 'Hemat'];
        $nouns = ['Snack', 'Minuman', 'Kopi', 'Teh', 'Mie', 'Roti', 'Coklat', 'Jus', 'Susu', 'Air Mineral'];
        $brands = ['Indofood', 'Sosro', 'Mayora', 'Ultrajaya', 'Kapal Api', 'Aqua', 'Nestle', 'Wingsfood', 'ABC', 'Garudafood'];
        return $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)] . ' ' . $brands[array_rand($brands)];
    }
    public function create(int $count = 1): void {
        $this->db->query("INSERT INTO products (nama_produk, harga, stok, foto) VALUES (:nama, :harga, :stok, :foto)");
        for ($i = 0; $i < $count; $i++) {
            $nama = $this->getRandomProductName();
            $harga = rand(3, 20) * 1000;
            $stok = rand(10, 100);
            $foto = 'default.jpg';
            $this->db->bind(':nama', $nama);
            $this->db->bind(':harga', $harga);
            $this->db->bind(':stok', $stok);
            $this->db->bind(':foto', $foto);
            $this->db->execute();
        }
        echo "{$count} produk berhasil dibuat.\n";
    }
}