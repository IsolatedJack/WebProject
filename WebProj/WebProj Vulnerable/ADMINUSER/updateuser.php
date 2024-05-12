<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['privileges'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $privileges = $_POST['privileges'];

    // Database connection parameters
    $db_host = 'localhost';
    $db_name = 'blog_website';
    $db_user = 'root';
    $db_password = '';

    // Connect to the database
    try {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

    try {
        // Check for duplicate username
        $query = $db->prepare("SELECT user_id FROM blog_users WHERE username = :username AND user_id != :user_id");
        $query->bindParam(':username', $username);
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $existingUsername = $query->fetch();

        // Check for duplicate email
        $query = $db->prepare("SELECT user_id FROM blog_users WHERE email = :email AND user_id != :user_id");
        $query->bindParam(':email', $email);
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $existingEmail = $query->fetch();

        // Check for duplicate phone number
        $query = $db->prepare("SELECT user_id FROM blog_users WHERE phone_number = :phone_number AND user_id != :user_id");
        $query->bindParam(':phone_number', $phone_number);
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $existingPhoneNumber = $query->fetch();

        // If duplicates found, redirect back to edit user page with error message
        if ($existingUsername || $existingEmail || $existingPhoneNumber) {
            header("Location: edituser.php?id=$user_id&error=duplicate");
            exit();
        }

        // Prepare SQL statement to update user
        $query = $db->prepare("UPDATE blog_users SET username = :username, name = :name, email = :email, phone_number = :phone_number, date_of_birth = :date_of_birth, privileges = :privileges WHERE user_id = :user_id");
        $query->bindParam(':username', $username);
        $query->bindParam(':name', $name);
        $query->bindParam(':email', $email);
        $query->bindParam(':phone_number', $phone_number);
        $query->bindParam(':date_of_birth', $date_of_birth);
        $query->bindParam(':privileges', $privileges);
        $query->bindParam(':user_id', $user_id);
        
        // Execute the update query
        $query->execute();

        // Redirect to admin user page with success message
        header("Location: adminuser.php?update=success");
        exit();
    } catch(PDOException $e) {
        // Redirect to admin user page with error message
        header("Location: adminuser.php?update=error");
        exit();
    }
} else {
    // If the form is not submitted, redirect to edit user page
    header("Location: edituser.php?id=$user_id");
    exit();
}
?>
