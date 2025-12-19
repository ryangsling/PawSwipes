<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

include 'config.php';
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
   
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
       
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error_message = 'Email address is already registered.';
        } else {
            
            $verification_token = bin2hex(random_bytes(32));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, verification_token, is_verified, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            
            if ($stmt->execute([$first_name, $last_name, $email, $hashed_password, $verification_token])) {
                
                $mail = new PHPMailer(true);
                
                try {
                    
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; 
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'roymustang30594@gmail.com'; 
                    $mail->Password   = 'hraiqhmggnpjvlyv';    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    
                    
                    $mail->setFrom('roymustang30594@gmail.com', 'PawSwipes');
                    $mail->addAddress($email, $first_name . ' ' . $last_name);
                    
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your PawSwipes Account';
                    $verification_link = "http://localhost/PAWSWIPES/verify.php?token=" . $verification_token;
                    $mail->Body = "
                        <h2>Welcome to PawSwipes!</h2>
                        <p>Hi $first_name,</p>
                        <p>Thank you for registering with PawSwipes! Please click the link below to verify your email address:</p>
                        <p><a href='$verification_link' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a></p>
                        <p>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p>$verification_link</p>
                        <p>Happy swiping!</p>
                        <p>The PawSwipes Team</p>
                    ";
                    
                    $mail->send();
                    
                    
                    $_SESSION['success_message'] = 'Registration successful! Please check your email to verify your account.';
                    header('Location: login.php');
                    exit();
                } catch (Exception $e) {
                    
                    $error_message = 'Registration successful, but verification email could not be sent. Please contact support.';
                }
            } else {
                $error_message = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - PawSwipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="gradient-background">
    
    <div class="position-fixed top-0 end-0 m-3">
        <button type="button" class="btn btn-outline-light" id="themeToggle">
            <span class="theme-icon">🌙</span>
        </button>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Join PawSwipes</h2>
                            <p class="text-muted">Create an account to find your dog's perfect match!</p>
                        </div>

                        <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['success_message']);
                                unset($_SESSION['success_message']);
                            ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" id="registerForm" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required />
                                    <div class="invalid-feedback">Please enter your first name.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required />
                                    <div class="invalid-feedback">Please enter your last name.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required />
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6" />
                                <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required />
                                <div class="invalid-feedback">Please confirm your password.</div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Register</button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">
                                Already have an account?
                                <a href="login.php" class="text-decoration-none">Login here</a>
                            </p>
                            <p class="mt-2">
                                <a href="index.php" class="text-muted text-decoration-none">← Back to Home</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
