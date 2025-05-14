<?php
namespace App\Services;

use PDO;
use PDOException;

class DatabaseService {
    private $pdo;

    public function __construct() {
        $dbPath = __DIR__ . '/../../database/recruitment.sqlite'; // adjust if needed
        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function saveApplicant($data) {
        $sql = "INSERT INTO applicants (name, email, phone, resume_path, score, status, created_at) 
                VALUES (:name, :email, :phone, :resume_path, :score, :status, datetime('now'))";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':resume_path' => $data['resume_path'],
            ':score' => $data['score'],
            ':status' => $data['status']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getApplicant($id) {
        $sql = "SELECT * FROM applicants WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateApplicantStatus($id, $status) {
        $sql = "UPDATE applicants SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }
}
