<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // Show the registration form
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
        <h1>Registration</h1>
        <form action="/Register.php" method="post">
            <div class="form-row">
                <input type="text" name="fname" placeholder="First name" value="<?php echo isset($_GET['fname']) ? htmlspecialchars($_GET['fname']) : ''; ?>">
            </div>
            <div class="form-row">
                <input type="text" name="lname" placeholder="Last name" value="<?php echo isset($_GET['lname']) ? htmlspecialchars($_GET['lname']) : ''; ?>">
            </div>
            <div class="form-row">
                <input type="text" name="username" placeholder="Username" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>">
            </div>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <div class="form-row">
                <input type="password" name="password1" placeholder="Password" id="password1">
            </div>
            <div class="form-row">
                <input type="password" name="password2" placeholder="Repeat Password" id="password2">
            </div>
            <div id="password-error"></div>
            <div class="form-row">
                <input type="submit" value="Register">
            </div>
        </form>

        <div class="center">
            Not registered yet? <a href="/Register.php">Register</a>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                var password1 = $('#password1').val();
                var password2 = $('#password2').val();

                $('.error-message').remove();

                if (password1 !== password2) {
                    var errorMessage = $('<div id="error_message" class="error-message">Passwords do not match!</div>');
                    $('#password-error').append(errorMessage);
                } else {
                    this.submit();
                }
            });
        });

    </script>
    </body>
    </html>

    <?php
}
else if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Process the registration

    include_once 'Config.php';

    if (isset($_POST['fname'], $_POST['lname'], $_POST['username'], $_POST['password1'])) {
        $firstName = $conn->real_escape_string($_POST['fname']);
        $lastName = $conn->real_escape_string($_POST['lname']);
        $username = $conn->real_escape_string($_POST['username']);
        $password1 = $_POST['password1'];

        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();

            $queryParams = http_build_query([
                'error' => 'Username is already taken',
                'fname' => $firstName,
                'lname' => $lastName,
                'username' => $username,
            ]);
            header("Location: Register.php?$queryParams");
            exit();
        }
        $stmt->close();

        $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, password, first_name, last_name, balance, registered_on) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $defaultBalance = 0.0;
        $stmt->bind_param("ssssd", $username, $hashedPassword, $firstName, $lastName, $defaultBalance);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;
            $_SESSION['user_id'] = $userId;
            $_SESSION['fname'] = $firstName;
            $_SESSION['balance'] = 0;

            $stmt->close();

            header("Location: Game.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please provide all required fields.";
    }

// Close the database connection
    $conn->close();
}
else
{
    echo 'Request method is not allowed';
}