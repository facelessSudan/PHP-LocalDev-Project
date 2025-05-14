<?php
namespace App\Services;

class FileStorageService {
    private $uploadDir;
    
    public function __construct() {
        $this->uploadDir = __DIR__ . '/../../uploads/resumes/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function saveResume($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload failed');
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $this->uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to save file');
        }
        
        return $filepath;
    }
    
    public function extractTextFromPDF($filepath) {
        // In production, am aought to use libraries like pdfparser/pdfparser
        
        // For now, am returning mock text for demonstration purposes.
        return "Faceless Sudan\nEmail: sudan@proton.me\nPhone: +254 722123456\n\n
                EXPERIENCE:\nJunior PHP Developer at ALX SE Program (2022-2023)\n
                - Developed REST APIs using Laravel\n
                - Implemented microservices architecture\n
                - Led team of 3 developers\n\n
                SKILLS:\nPHP, Laravel, MySQL, JavaScript, Docker, Git\n\n
                EDUCATION:\nBachelor of Community Development";
    }
}
