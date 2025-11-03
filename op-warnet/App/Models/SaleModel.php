<?php
class SaleModel {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function recordSale(int $sessionId, int $productId, int $quantity): bool {
        $this->db->query("SELECT harga, stok FROM products WHERE id = :product_id AND deleted_at IS NULL");
        $this->db->bind(':product_id', $productId);
        $product = $this->db->single();
        if (!$product || $product['stok'] < $quantity) {
            return false;
        }
        $pricePerItem = (int)$product['harga'];
        $totalPrice = $pricePerItem * $quantity;
        $saleQuery = "INSERT INTO sales (billing_session_id, product_id, quantity, price_per_item, total_price)
                    VALUES (:session_id, :product_id, :quantity, :price, :total)";
        $this->db->query($saleQuery);
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':price', $pricePerItem);
        $this->db->bind(':total', $totalPrice);
        $this->db->execute();
        $saleSuccess = $this->db->rowCount() > 0;
        if ($saleSuccess) {
            $updateStokQuery = "UPDATE products SET stok = stok - :quantity WHERE id = :product_id";
            $this->db->query($updateStokQuery);
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            return true;
        }
        return false;
    }

    public function getSalesForSession(int $sessionId): array {
        $query = "SELECT s.*, p.nama_produk
                FROM sales s
                JOIN products p ON s.product_id = p.id
                WHERE s.billing_session_id = :session_id
                ORDER BY s.sale_time DESC";
        $this->db->query($query);
        $this->db->bind(':session_id', $sessionId);
        return $this->db->resultSet();
    }
}