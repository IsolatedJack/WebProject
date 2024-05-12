<?php
session_start();

// Database connection parameters
$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['privileges'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if user_id parameter is set and is numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Check if the user is trying to delete their own account
    if ($user_id === $_SESSION['user_id']) {
        // Redirect back to admin users page with an error message
        $_SESSION['error_message'] = "You cannot delete your own account.";
        header("Location: adminuser.php");
        exit();
    }

    // Check if the user to be deleted is an admin
    try {
        // Connect to the database
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute SQL statement to check if the user is an admin
        $stmt = $db->prepare("SELECT privileges FROM blog_users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the user is an admin, prevent deletion
        if ($user['privileges'] === 'admin') {
            echo"<script>alert('Cannot delete an admin account'); window.location.href='adminuser.php'</script>";
            exit();
        }

        // Proceed with deleting non-admin user
        $stmt = $db->prepare("DELETE FROM blog_users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Redirect back to admin users page after deletion
        header("Location: adminuser.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Redirect back to admin users page if user_id parameter is missing or not numeric
    header("Location: adminuser.php");
    exit();
}
?>
