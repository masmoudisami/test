<?php
class Client {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM clients ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function searchByModel($search = '') {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE car_model LIKE ? OR name LIKE ? ORDER BY car_model ASC, name ASC");
        $searchTerm = "%" . $search . "%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $car_model, $phone, $address) {
        $stmt = $this->db->prepare("INSERT INTO clients (name, car_model, phone, address) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $car_model, $phone, $address]);
    }

    public function update($id, $name, $car_model, $phone, $address) {
        $stmt = $this->db->prepare("UPDATE clients SET name=?, car_model=?, phone=?, address=? WHERE id=?");
        return $stmt->execute([$name, $car_model, $phone, $address, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllModels() {
        $stmt = $this->db->query("SELECT DISTINCT car_model FROM clients WHERE car_model IS NOT NULL AND car_model != '' ORDER BY car_model ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}