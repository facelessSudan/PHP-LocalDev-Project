<?php
namespace App;

use App\Services\DatabaseService;
use App\Services\LocalMailService;
use App\Services\LocalAIService;
use App\Services\FileStorageService;

class AIRecruitmentAgent {
    private $db;
    private $mail;
    private $ai;
    private $storage;
    private $n8nWebhookUrl;
    
    public function __construct() {
        $this->db = new DatabaseService();
        $this->mail = new LocalMailService();
        $this->ai = new LocalAIService();
        $this->storage = new FileStorageService();
        $this->n8nWebhookUrl = $_ENV['N8N_WEBHOOK_URL'] ?? 'http://localhost:5678/webhook/resume-processing';
    }
    
    public function processApplication($formData, $resumeFile) {
        try {
            // 1. Save resume file locally
            $resumePath = $this->storage->saveResume($resumeFile);
            
            // 2. Extract text from resume
            $resumeText = $this->storage->extractTextFromPDF($resumePath);
            
            // 3. Get job description 
            $jobDescription = $this->getJobDescription($formData['job_id'] ?? 1);
            
            // 4. Score resume using local AI
            $score = $this->ai->scoreResume($resumeText, $jobDescription);
            
            // 5. Save to database
            $applicantData = [
                'name' => $formData['name'],
                'email' => $formData['email'],
                'phone' => $formData['phone'] ?? '',
                'resume_path' => $resumePath,
                'score' => $score,
                'status' => $score >= 70 ? 'qualified' : 'review'
            ];
            
            $applicantId = $this->db->saveApplicant($applicantData);
            
            // 6. Send to n8n for workflow processing
            $this->sendToN8n([
                'applicant_id' => $applicantId,
                'name' => $formData['name'],
                'email' => $formData['email'],
                'score' => $score,
                'resume_text' => $resumeText,
                'job_description' => $jobDescription
            ]);
            
            // 7. Send confirmation email
            $this->sendConfirmationEmail($formData['email'], $formData['name']);
            
            return [
                'success' => true,
                'message' => 'Application submitted successfully',
                'applicant_id' => $applicantId,
                'score' => $score
            ];
            
        } catch (\Exception $e) {
            error_log('Error processing application: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing application',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function sendToN8n($data) {
        $client = new \GuzzleHttp\Client();
        
        try {
            $response = $client->post($this->n8nWebhookUrl, [
                'json' => $data
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            error_log('Error sending to n8n: ' . $e->getMessage());
            // Continue processing even if n8n fails
        }
    }
    
    private function sendConfirmationEmail($email, $name) {
        $subject = 'Application Received - Thank You!';
        $body = "
        <html>
        <body>
            <h2>Dear {$name},</h2>
            <p>Thank you for submitting your application. We have received your resume and will review it shortly.</p>
            <p>You will hear from us within 3-5 business days.</p>
            <p>Best regards,<br>HR Department</p>
        </body>
        </html>
        ";
        
        return $this->mail->sendEmail($email, $subject, $body);
    }
    
    private function getJobDescription($jobId) {
        // In a real app, this would fetch from database
        return "We are looking for a skilled PHP developer with experience in Laravel, 
                REST APIs, and modern web development practices. Must have strong 
                problem-solving skills and ability to work in a team.";
    }
}
