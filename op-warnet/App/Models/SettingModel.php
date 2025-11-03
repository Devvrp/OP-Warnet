<?php
class SettingModel {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }
    public function getSetting($key) {
        $this->db->query("SELECT setting_value FROM settings WHERE setting_key = :key");
        $this->db->bind('key', $key);
        $result = $this->db->single();
        return $result['setting_value'];
    }
}