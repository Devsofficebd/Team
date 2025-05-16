<?php
session_start();

if ($_SESSION['logged_in'] ?? false) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'pass') {
        $_SESSION['logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5" style="max-width: 400px;">

    <h3 class="mb-4 text-center">Admin Login</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
    </form>

</body>
</html>
