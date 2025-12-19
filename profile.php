<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error_message = '';
$success_message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $location = trim($_POST['location']);
    
    
    if (empty($first_name) || empty($last_name)) {
        $error_message = 'First name and last name are required.';
    } else {
        
        $profile_image = $user['profile_image']; // Keep existing image by default
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['profile_image']['type'], $allowed_types) && $_FILES['profile_image']['size'] <= $max_size) {
                $upload_dir = 'uploads/profiles/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    if ($user['profile_image'] && file_exists('uploads/profiles/' . $user['profile_image'])) {
                        unlink('uploads/profiles/' . $user['profile_image']);
                    }
                    $profile_image = $new_filename;
                } else {
                    $error_message = 'Failed to upload profile image.';
                }
            } else {
                $error_message = 'Invalid image file. Please upload JPEG, PNG, or GIF files under 5MB.';
            }
        }
        
        if (!$error_message) {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, bio = ?, location = ?, profile_image = ? WHERE id = ?");
            
            if ($stmt->execute([$first_name, $last_name, $phone, $bio, $location, $profile_image, $_SESSION['user_id']])) {
                $success_message = 'Profile updated successfully!';
                
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
            } else {
                $error_message = 'Failed to update profile. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - PawSwipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="gradient-background">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">🐾 PawSwipes</a>
            
            <button type="button" class="btn btn-outline-light me-2" id="themeToggle">
                <span class="theme-icon">🌙</span>
            </button>
            
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <?php echo htmlspecialchars($user['first_name']); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="profile.php">Edit Profile</a></li>
                    <li><a class="dropdown-item" href="swipe.php">Start Swiping</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="fw-bold text-primary text-center mb-4">Edit Profile</h2>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="profileForm" novalidate>
                            <!-- Profile Image -->
                            <div class="text-center mb-4">
                                <?php if ($user['profile_image']): ?>
                                    <img src="uploads/profiles/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                         class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                                         style="width: 120px; height: 120px;">
                                        <span class="text-white fs-1">👤</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                    <div class="form-text">Upload JPEG, PNG, or GIF files under 5MB</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    <div class="invalid-feedback">Please enter your first name.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    <div class="invalid-feedback">Please enter your last name.</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <div class="form-text">Email cannot be changed</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" 
                                       placeholder="e.g., New York, NY">
                            </div>
                            
                            <div class="mb-4">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" 
                                          placeholder="Tell us about yourself and what you're looking for..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Update Profile</button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <a href="dashboard.php" class="text-muted text-decoration-none">← Back to Dashboard</a>
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