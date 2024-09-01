<?php
if (!file_exists('Config.php')) {
    // Redirect to the installation script if Config.php does not exist
    header('Location: Install.php');
    exit();
}

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /Game.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>

    <link rel="stylesheet" type="text/css" href="styles.css">

    <!-- Latest jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <form action="/Login.php" method="post">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <div class="form-row">
            <input type="text" name="username" placeholder="Username">
        </div>
        <div class="form-row">
            <input type="password" name="password" placeholder="Password">
        </div>
        <div class="form-row">
            <input type="submit" value="Log In">
        </div>
    </form>

    <div class="center">
        Not registered yet? <a href="/Register.php">Register</a>
    </div>
</div>
</body>
</html>
