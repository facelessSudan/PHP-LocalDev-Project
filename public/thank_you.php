<?php
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You for Applying</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="10;url=index.php"> <!-- Auto-redirect -->
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .thank-you-container {
            background: #fff;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #28a745;
        }
        .app-id {
            margin: 1rem 0;
            background-color: #e9f7ef;
            padding: 1rem;
            border-radius: 8px;
            font-weight: bold;
            color: #155724;
        }
        p {
            color: #333;
        }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <h1> Thank You for Your Application!</h1>
        <?php if ($id): ?>
            <div class="app-id">Your Application ID: <?= htmlspecialchars($id) ?></div>
        <?php endif; ?>
        <p>We’ve received your resume. Our team will get back to you soon.</p>
        <p>You’ll be redirected to the application page in 30 seconds.</p>
        
        <a class="btn" href="index.php">Back to Application</a>

        <div class="footer">Recruitment Portal &copy; <?= date("Y") ?></div>
    </div>
</body>
</html>

