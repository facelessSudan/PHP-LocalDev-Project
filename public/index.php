<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\AIRecruitmentAgent;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize the application
$agent = new AIRecruitmentAgent();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $agent->processApplication($_POST, $_FILES['resume']);
    
    if ($result['success']) {
        header('Location: thank_you.php?id=' . $result['applicant_id']);
        exit;
    } else {
        $error = $result['message'];
    }
}

// Include the view
include __DIR__ . '/../src/views/application_form.php';
?>
