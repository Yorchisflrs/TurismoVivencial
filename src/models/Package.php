<?php
// src/models/Package.php
class Package {
    public static function create($host_id, $title, $location, $category, $price, $max_participants, $description) {
        global $pdo;
        $stmt = $pdo->prepare('
            INSERT INTO packages (host_id, title, location, category, price, max_participants, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, "PENDING")
        ');
        return $stmt->execute([$host_id, $title, $location, $category, $price, $max_participants, $description]);
    }
    
    public static function getPending() {
        global $pdo;
        $stmt = $pdo->query('
            SELECT p.*, h.id as host_id, h.full_name as host_name 
            FROM packages p 
            JOIN hosts h ON p.host_id = h.id
            WHERE p.status = "PENDING"
            ORDER BY p.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getApproved() {
        global $pdo;
        $stmt = $pdo->query('
            SELECT p.*, h.id as host_id, h.full_name as host_name 
            FROM packages p 
            JOIN hosts h ON p.host_id = h.id AND h.status = "APPROVED"
            WHERE p.status = "APPROVED"
            ORDER BY p.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function approve($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE packages SET status = "APPROVED" WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public static function reject($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE packages SET status = "REJECTED" WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public static function findById($id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM packages WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
