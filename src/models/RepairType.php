<?php
class RepairType {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM repair_types ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM repair_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $price) {
        $stmt = $this->db->prepare("INSERT INTO repair_types (name, default_price) VALUES (?, ?)");
        return $stmt->execute([$name, $price]);
    }

    public function update($id, $name, $price) {
        $stmt = $this->db->prepare("UPDATE repair_types SET name=?, default_price=? WHERE id=?");
        return $stmt->execute([$name, $price, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM repair_types WHERE id = ?");
        return $stmt->execute([$id]);
    }
}