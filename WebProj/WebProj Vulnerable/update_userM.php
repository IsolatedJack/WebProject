<?php
session_start();

$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['username'])) {
        if (isset($_GET['update'])) { // Check if it's an update request
            $old_username = $_SESSION['username'];

            // Get user input from the URL parameters
            $name = $_GET['name'];
            $new_username = $_GET['username'];
            $email = $_GET['email'];
            $phone_number = $_GET['phone_number'];

            // Check if the new username is not already taken
            $query_check = $db->prepare("SELECT COUNT(*) FROM blog_users WHERE username = :new_username AND username != :old_username");
            $query_check->bindParam(':new_username', $new_username);
            $query_check->bindParam(':old_username', $old_username);
            $query_check->execute();
            $count = $query_check->fetchColumn();

            if ($count > 0) {
                // Username already exists, redirect back with an error message
                $_SESSION['error'] = "Username already exists. Please choose a different one.";
                header("Location: useraccount.php");
                exit();
            }

            // Prepare SQL statement to update user info
            $query = $db->prepare("UPDATE blog_users SET name = :name, username = :new_username, email = :email, phone_number = :phone_number WHERE username = :old_username");
            $query->bindParam(':name', $name);
            $query->bindParam(':new_username', $new_username);
            $query->bindParam(':email', $email);
            $query->bindParam(':phone_number', $phone_number);
            $query->bindParam(':old_username', $old_username);

            // Execute the query
            $query->execute();

            // Update session variable if the username has changed
            if ($old_username !== $new_username) {
                $_SESSION['username'] = $new_username;
            }

            // Redirect back to useraccount.php after updating
            header("Location: useraccount.php");
            exit();
        } elseif (isset($_GET['change_password'])) {
            // Handle password change
            $new_password = $_GET['new_password'];
            $confirm_password = $_GET['confirm_password'];

            // Verify old password
            $username = $_SESSION['username'];
            $query = $db->prepare("SELECT password FROM blog_users WHERE username = :username");
            $query->bindParam(':username', $username);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $hashed_password = $result['password'];

            // Validate new password
            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "New passwords do not match.";
                echo "<script>alert('New passwords do not match.'); window.location.href='useraccount.php'</script>";
                exit();
            }

            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $query_update_password = $db->prepare("UPDATE blog_users SET password = :password WHERE username = :username");
            $query_update_password->bindParam(':password', $hashed_new_password);
            $query_update_password->bindParam(':username', $username);
            $query_update_password->execute();

            $_SESSION['success'] = "Password changed successfully.";
            echo "<script>alert('Password changed successfully.'); window.location.href='useraccount.php'</script>";
            exit();
        }
    } else {
        // Redirect to the login page if the session variable is not set
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>