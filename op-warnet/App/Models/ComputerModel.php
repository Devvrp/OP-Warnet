<?php
class ComputerModel {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }
    public function getAllComputerSessions() {
        $this->db->query("SELECT c.id, c.nomor_pc, c.status, bs.id as session_id, UNIX_TIMESTAMP(bs.end_time) as end_time_unix FROM computers c LEFT JOIN billing_sessions bs ON c.current_session_id = bs.id ORDER BY c.nomor_pc ASC");
        return $this->db->resultSet();
    }
    public function getAllComputers() {
        $this->db->query('SELECT * FROM computers ORDER BY nomor_pc ASC');
        return $this->db->resultSet();
    }
    public function tambahDataComputer($data) {
        $this->db->query("INSERT INTO computers (nomor_pc) VALUES (:nomor_pc)");
        $this->db->bind('nomor_pc', $data['nomor_pc']);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function hapusDataComputer($id) {
        $this->db->query("DELETE FROM computers WHERE id = :id");
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function ubahDataComputer($data) {
        $this->db->query("UPDATE computers SET nomor_pc = :nomor_pc, status = :status WHERE id = :id");
        $this->db->bind('nomor_pc', $data['nomor_pc']);
        $this->db->bind('status', $data['status']);
        $this->db->bind('id', $data['id']);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function startNewSession($data) {
        $this->db->query("INSERT INTO billing_sessions (computer_id, start_time, end_time, total_biaya) VALUES (:computer_id, NOW(), DATE_ADD(NOW(), INTERVAL :duration SECOND), :nominal)");
        $this->db->bind('computer_id', $data['computer_id']);
        $this->db->bind('duration', $data['duration']);
        $this->db->bind('nominal', $data['nominal']);
        $this->db->execute();
        $sessionId = $this->db->lastInsertId();
        $this->db->query("UPDATE computers SET status = 'digunakan', current_session_id = :session_id WHERE id = :computer_id");
        $this->db->bind('session_id', $sessionId);
        $this->db->bind('computer_id', $data['computer_id']);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function endSession($sessionId) {
        $this->db->query("SELECT computer_id FROM billing_sessions WHERE id=:id");
        $this->db->bind('id', $sessionId);
        $session = $this->db->single();
        if(!$session) return 0;
        $computerId = $session['computer_id'];
        $this->db->query("UPDATE billing_sessions SET status = 'selesai', end_time = NOW() WHERE id = :id");
        $this->db->bind('id', $sessionId);
        $this->db->execute();
        $this->db->query("UPDATE computers SET status = 'tersedia', current_session_id = NULL WHERE id = :computer_id");
        $this->db->bind('computer_id', $computerId);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function addDurationToSession($data) {
        $this->db->query("UPDATE billing_sessions SET end_time = DATE_ADD(end_time, INTERVAL :duration SECOND), total_biaya = total_biaya + :nominal WHERE id = :session_id");
        $this->db->bind('duration', $data['duration']);
        $this->db->bind('nominal', $data['nominal']);
        $this->db->bind('session_id', $data['session_id']);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function getAllFrozenSessions() {
        $this->db->query("SELECT * FROM frozen_sessions ORDER BY created_at DESC");
        return $this->db->resultSet();
    }
    public function freezeSession($data) {
        $this->db->query("SELECT UNIX_TIMESTAMP(end_time) as end_unix FROM billing_sessions WHERE id=:id");
        $this->db->bind('id', $data['session_id']);
        $session = $this->db->single();
        if(!$session) return 0;
        $remainingSeconds = $session['end_unix'] - time();
        if ($remainingSeconds < 0) $remainingSeconds = 0;
        $this->db->query("INSERT INTO frozen_sessions (customer_name, remaining_seconds) VALUES (:customer_name, :remaining_seconds)");
        $this->db->bind('customer_name', $data['customer_name']);
        $this->db->bind('remaining_seconds', $remainingSeconds);
        $this->db->execute();
        return $this->endSession($data['session_id']);
    }
    public function resumeFrozenSession($data) {
        $this->db->query("SELECT remaining_seconds FROM frozen_sessions WHERE id=:id");
        $this->db->bind('id', $data['frozen_id']);
        $frozen = $this->db->single();
        if(!$frozen) return 0;
        $duration = $frozen['remaining_seconds'];
        require_once __DIR__ . '/SettingModel.php';
        $settingModel = new SettingModel();
        $rate = $settingModel->getSetting('billing_rate_per_hour');
        $nominal = round(($duration / 3600) * $rate);
        $sessionData = ['computer_id' => $data['computer_id'], 'duration' => $duration, 'nominal' => $nominal];
        $this->startNewSession($sessionData);
        $this->db->query("DELETE FROM frozen_sessions WHERE id = :id");
        $this->db->bind('id', $data['frozen_id']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateStatus(int $computerId, string $status): bool {
        $this->db->query("UPDATE computers SET status = :status, current_session_id = NULL WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $computerId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}