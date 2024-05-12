<?php
session_start();

$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

function validatePassword($password) {
    // Password should be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    }
    return true;
}

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['username'])) {
        if (isset($_POST['update'])) { // Check if it's an update request
            $old_username = $_SESSION['username'];

            // Get user input from the form
            $name = $_POST['name'];
            $new_username = $_POST['username'];
            $email = $_POST['email'];
            $phone_number = $_POST['phone_number'];

            // Check if the new username is not already taken
            $query_check = $db->prepare("SELECT COUNT(*) FROM blog_users WHERE username = :new_username AND username != :old_username");
            $query_check->bindParam(':new_username', $new_username);
            $query_check->bindParam(':old_username', $old_username);
            $query_check->execute();
            $count = $query_check->fetchColumn();

            if ($count > 0) {
                // Username already exists, redirect back with an error message
                $_SESSION['error'] = "Username already exists. Please choose a different one.";
                echo "<script>alert('Username already exists. Please choose a different one.'); window.location.href='useraccount.php'</script>";
                exit();
            }

             // Check if the email or phone number already exists for a different user
             $query_check_email_phone = $db->prepare("SELECT COUNT(*) FROM blog_users WHERE (email = :email OR phone_number = :phone_number) AND username != :old_username");
             $query_check_email_phone->bindParam(':email', $email);
             $query_check_email_phone->bindParam(':phone_number', $phone_number);
             $query_check_email_phone->bindParam(':old_username', $old_username);
             $query_check_email_phone->execute();
             $count_email_phone = $query_check_email_phone->fetchColumn();
 
             if ($count_email_phone > 0) {
                 // Email or phone number already exists for a different user, reject the update
                 $_SESSION['error'] = "Email or phone number already exists for another user.";
                 echo "<script>alert('Email or phone number already exists for another user.'); window.location.href='useraccount.php'</script>";
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
        } elseif (isset($_POST['change_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Verify old password
            $username = $_SESSION['username'];
            $query = $db->prepare("SELECT password FROM blog_users WHERE username = :username");
            $query->bindParam(':username', $username);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $stored_password = $result['password'];

            if (!password_verify($old_password, $stored_password)) {
                $_SESSION['error'] = "Old password is incorrect.";
                echo "<script>alert('Old password is incorrect.'); window.location.href='useraccount.php'</script>";
                exit();
            }


            // Validate new password
            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "New passwords do not match.";
                echo "<script>alert('New passwords do not match.'); window.location.href='useraccount.php'</script>";
                exit();
            }

            if (!validatePassword($new_password)) {
                $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
                echo "<script>alert('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character.'); window.location.href='useraccount.php'</script>";
                exit();
            }

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
