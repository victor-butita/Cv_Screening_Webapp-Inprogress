<?php
include 'db_conn.php';

// Function to delete a user
function deleteUser($conn, $user_id) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Handle deleting user
if (isset($_POST['action']) && $_POST['action'] === 'deleteUser' && isset($_POST['user_id'])) {
    deleteUser($conn, $_POST['user_id']);
}
?>
