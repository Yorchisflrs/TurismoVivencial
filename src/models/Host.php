<?php
// src/models/Host.php
class Host {
    public static function create($user_id, $bio = null, $docs_path = null) {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO hosts (user_id, bio, docs_path, status) VALUES (?, ?, ?, "PENDING")');
        return $stmt->execute([$user_id, $bio, $docs_path]);
    }
    
    public static function findByUserId($user_id) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM hosts WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getPending() {
        global $pdo;
        $stmt = $pdo->query('
            SELECT h.*, 
                   COALESCE(u.name, h.full_name) as name, 
                   COALESCE(u.email, h.email) as email
            FROM hosts h 
            LEFT JOIN users u ON h.user_id = u.id 
            WHERE h.status = "PENDING"
            ORDER BY h.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function approve($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE hosts SET status = "APPROVED" WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public static function reject($id) {
        global $pdo;
        $stmt = $pdo->prepare('UPDATE hosts SET status = "REJECTED" WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
