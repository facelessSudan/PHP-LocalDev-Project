<?php
namespace App\Services;

class LocalMailService {
    private $mailhogUrl;
    
    public function __construct() {
        $this->mailhogUrl = $_ENV['MAILHOG_URL'] ?? 'http://localhost:8025';
    }
    
    public function sendEmail($to, $subject, $body) {
        // Since we're using MailHog for local development, we'll use PHP's mail function
        // MailHog will catch all emails sent on port 1025
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@recruitment.local" . "\r\n";
        
        // In development, we'll log the email
        error_log("Sending email to: $to, Subject: $subject");
        
        // Use mail() function which MailHog will intercept
        $result = mail($to, $subject, $body, $headers);
        
        // I have an option of also sending directly to MailHog's SMTP server
        // ini_set('SMTP', 'localhost');
        // ini_set('smtp_port', '1025');
        
        return $result;
    }
    
    public function sendBulkEmails($recipients, $subject, $body) {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->sendEmail($recipient, $subject, $body);
        }
        
        return $results;
    }
}
