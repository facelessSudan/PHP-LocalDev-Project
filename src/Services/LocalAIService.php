<?php
namespace App\Services;

class LocalAIService {
    private $keywords = [
        'php' => 15,
        'laravel' => 15,
        'javascript' => 10,
        'mysql' => 10,
        'api' => 10,
        'rest' => 10,
        'git' => 5,
        'docker' => 5,
        'agile' => 5,
        'team' => 5,
        'experience' => 10
    ];
    
    public function scoreResume($resumeText, $jobDescription) {
        $score = 0;
        $resumeLower = strtolower($resumeText);
        $jobLower = strtolower($jobDescription);
        
        // Keyword matching
        foreach ($this->keywords as $keyword => $points) {
            if (stripos($resumeLower, $keyword) !== false) {
                $score += $points;
            }
        }
        
        // Experience years extraction
        if (preg_match('/(\d+)\s*years?\s*(of\s*)?experience/i', $resumeText, $matches)) {
            $years = intval($matches[1]);
            $score += min($years * 5, 25); // Max 25 points for experience
        }
        
        // Education level
        if (stripos($resumeLower, 'bachelor') !== false || stripos($resumeLower, 'master') !== false) {
            $score += 10;
        }
        
        // Normalize score to 100
        return min($score, 100);
    }
    
    public function generateFeedback($score, $resumeText) {
        $feedback = [];
        
        if ($score >= 80) {
            $feedback[] = "Excellent match for the position!";
        } elseif ($score >= 60) {
            $feedback[] = "Good potential candidate.";
        } else {
            $feedback[] = "May need additional review.";
        }
        
        // Check for missing keywords
        $missing = [];
        foreach ($this->keywords as $keyword => $points) {
            if (stripos($resumeText, $keyword) === false && $points >= 10) {
                $missing[] = $keyword;
            }
        }
        
        if (!empty($missing)) {
            $feedback[] = "Missing key skills: " . implode(', ', $missing);
        }
        
        return implode(' ', $feedback);
    }
}
