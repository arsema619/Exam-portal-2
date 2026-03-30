<?php
include "../config/db.php";

// check if id is provided
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // delete user
    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully!";
        
        // redirect back (optional)
        header("Location: get_users.php?deleted=1");
        exit();

    } else {
        echo "Error deleting user: " . $conn->error;
    }

} else {
    echo "No user ID provided!";
}
?>