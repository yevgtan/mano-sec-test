<?php
include 'Config.php';
session_start();

if (isset($_POST['username'], $_POST['password']) && !empty(trim($_POST['username'])) && !empty(trim($_POST['password']))) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password, first_name, balance FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword, $firstName, $balance);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['fname'] = $firstName;
            $_SESSION['balance'] = $balance;

            $stmt->close();
            header("Location: Game.php");
            exit();
        } else {
            $stmt->close();
            header("Location: /?error=User+not+found+or+Invalid+password");
            exit();
        }
    } else {
        $stmt->close();
        header("Location: /?error=User+not+found+or+Invalid+password");
        exit();
    }
} else {
    header("Location: /?error=Please+provide+both+username+and+password");
    exit();
}

// Close the database connection
$conn->close();