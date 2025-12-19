<?php
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    

    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
       
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        $message = "Email verified successfully! You can now login to your account.";
        $success = true;
    } else {
        $message = "Invalid or expired verification token.";
        $success = false;
    }
} else {
    $message = "No verification token provided.";
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - PawSwipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="gradient-background">

    <div class="position-fixed top-0 end-0 m-3">
        <button type="button" class="btn btn-outline-light" id="themeToggle">
            <span class="theme-icon">🌙</span>
        </button>
    </div>

    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <h2 class="fw-bold text-primary">PawSwipes</h2>
                            <h4 class="mb-3"><?php echo $success ? 'Verification Successful!' : 'Verification Failed'; ?></h4>
                        </div>
                        
                        <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                        
                        <?php if ($success): ?>
                            <a href="login.php" class="btn btn-primary btn-lg">Login Now</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary btn-lg">Register Again</a>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="index.php" class="text-muted text-decoration-none">← Back to Home</a>
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