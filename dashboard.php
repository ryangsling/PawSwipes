<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's dogs
$stmt = $pdo->prepare("SELECT * FROM dogs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$dogs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PawSwipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary gradient-background">
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
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body p-4 gradient-background">
                        <h1 class="display-6 fw-bold text-light">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                        <p class="lead text-light">Ready to find your furry friend's perfect match?</p>
                        <a href="swipe.php" class="btn btn-primary btn-lg">Start Swiping</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dogs Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Your Dogs</h2>
                    <a href="add_dog.php" class="btn btn-outline-primary">+ Add New Dog</a>
                </div>
                
                <?php if (empty($dogs)): ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <h4 class="text-muted">No dogs added yet</h4>
                            <p class="text-muted">Add your first dog to start finding matches!</p>
                            <a href="add_dog.php" class="btn btn-primary">Add Your First Dog</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($dogs as $dog): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card shadow-sm h-100">
                                    <?php if ($dog['image']): ?>
                                        <img src="uploads/dogs/<?php echo htmlspecialchars($dog['image']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <span class="text-muted fs-1">🐕</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($dog['name']); ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($dog['breed']); ?> • 
                                                <?php echo $dog['age']; ?> years • 
                                                <?php echo htmlspecialchars($dog['gender']); ?> • 
                                                <?php echo htmlspecialchars($dog['size']); ?>
                                            </small>
                                        </p>
                                        <p class="card-text"><?php echo htmlspecialchars($dog['description']); ?></p>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100">
                                            <a href="edit_dog.php?id=<?php echo $dog['id']; ?>" class="btn btn-outline-primary">Edit</a>
                                            <a href="delete_dog.php?id=<?php echo $dog['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this dog?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>