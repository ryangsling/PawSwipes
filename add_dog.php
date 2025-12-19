<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

$error_message = '';
$success_message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $size = $_POST['size'];
    $description = trim($_POST['description']);
    

    if (empty($name) || empty($breed) || $age <= 0 || empty($gender) || empty($size)) {
        $error_message = 'Please fill in all required fields.';
    } else {
    
        $dog_image = null;
        
        if (isset($_FILES['dog_image']) && $_FILES['dog_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['dog_image']['type'], $allowed_types) && $_FILES['dog_image']['size'] <= $max_size) {
                $upload_dir = 'uploads/dogs/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['dog_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'dog_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['dog_image']['tmp_name'], $upload_path)) {
                    $dog_image = $new_filename;
                } else {
                    $error_message = 'Failed to upload dog image.';
                }
            } else {
                $error_message = 'Invalid image file. Please upload JPEG, PNG, or GIF files under 5MB.';
            }
        }
        
        if (!$error_message) {
           
            $stmt = $pdo->prepare("INSERT INTO dogs (user_id, name, breed, age, gender, size, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$_SESSION['user_id'], $name, $breed, $age, $gender, $size, $description, $dog_image])) {
                $success_message = 'Dog added successfully!';
                
                $name = $breed = $description = '';
                $age = 0;
                $gender = $size = '';
            } else {
                $error_message = 'Failed to add dog. Please try again.';
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
    <title>Add Dog - PawSwipes</title>
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
                    Menu
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                    <li><a class="dropdown-item" href="profile.php">Edit Profile</a></li>
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
                        <h2 class="fw-bold text-primary text-center mb-4">Add Your Dog</h2>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success_message); ?>
                                <div class="mt-2">
                                    <a href="dashboard.php" class="btn btn-success btn-sm">View Dashboard</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="addDogForm" novalidate>
                            <div class="mb-3">
                                <label for="dog_image" class="form-label">Dog Photo</label>
                                <input type="file" class="form-control" id="dog_image" name="dog_image" accept="image/*">
                                <div class="form-text">Upload a clear photo of your dog (JPEG, PNG, or GIF under 5MB)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Dog's Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                <div class="invalid-feedback">Please enter your dog's name.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="breed" class="form-label">Breed *</label>
                                <input type="text" class="form-control" id="breed" name="breed" 
                                       value="<?php echo htmlspecialchars($breed ?? ''); ?>" 
                                       placeholder="e.g., Golden Retriever, Mixed" required>
                                <div class="invalid-feedback">Please enter your dog's breed.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="age" class="form-label">Age (years) *</label>
                                    <input type="number" class="form-control" id="age" name="age" 
                                           value="<?php echo htmlspecialchars($age ?? ''); ?>" min="1" max="20" required>
                                    <div class="invalid-feedback">Please enter your dog's age (1-20 years).</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Choose...</option>
                                        <option value="Male" <?php echo (isset($gender) && $gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo (isset($gender) && $gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                    <div class="invalid-feedback">Please select your dog's gender.</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="size" class="form-label">Size *</label>
                                <select class="form-select" id="size" name="size" required>
                                    <option value="">Choose...</option>
                                    <option value="Small" <?php echo (isset($size) && $size == 'Small') ? 'selected' : ''; ?>>Small (under 25 lbs)</option>
                                    <option value="Medium" <?php echo (isset($size) && $size == 'Medium') ? 'selected' : ''; ?>>Medium (25-60 lbs)</option>
                                    <option value="Large" <?php echo (isset($size) && $size == 'Large') ? 'selected' : ''; ?>>Large (over 60 lbs)</option>
                                </select>
                                <div class="invalid-feedback">Please select your dog's size.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Tell us about your dog's personality, favorite activities, etc..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Add Dog</button>
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