<?php
include 'Config.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];

if (!isset($_SESSION['credit']) || !is_numeric($_SESSION['credit']) || $_SESSION['credit'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credit amount.']);
    exit();
}

$creditAmount = $_SESSION['credit'];

try {
    $sql = "SELECT balance FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($currentBalance);
    $stmt->fetch();
    $stmt->close();

    $newBalance = $currentBalance + $creditAmount;

    $updateSql = "UPDATE users SET balance = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("di", $newBalance, $userId);

    if ($updateStmt->execute()) {
        $_SESSION['credit'] = 0;

        echo json_encode(['success' => true, 'newBalance' => $newBalance]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update balance.']);
    }

    $updateStmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();