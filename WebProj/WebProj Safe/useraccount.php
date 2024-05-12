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
        $username = $_SESSION['username'];
        $query = $db->prepare("SELECT * FROM blog_users WHERE username = :username");
        $query->bindParam(':username', $username);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
    } else {
        // Redirect to the login page if the session variable is not set
        header("Location: login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update'])) {
            // Handle user information update
            include 'update_user.php';
        } elseif (isset($_POST['change_password'])) {
            // Handle password change
            include 'update_user.php';
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="author" content="Abdulaziz">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Main</title>
<link rel="stylesheet" href="styles.css">
<style>
    /* Additional styles specific to this HTML */
    body {
            /* Animated gradient background */
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    .container {
        display:flex;
        flex-direction:column;
        align-items: center;
        padding: 20px;
        background-color: #ffffff;
        border-radius:15px;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        align-items:center;
    }
    .logo img {
        max-width: 200px;
        margin-bottom: 20px;
    }
    .search-bar {
        width: 300px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    .user-info {
        text-align: center;
        margin-bottom: 0px;
        align-self: flex-start; /* Align to the left */
        margin-left: 0px; /* Add some margin for spacing */
        padding: 20px; /* Add padding to create a box */
        border: 1.5px solid #000000; /* Add border */
        border-radius: 10px; /* Add border radius */
    }
    .user-info p {
        margin-bottom: 10px;
    }
    .logout-button {
        background-color: #e22b2b;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        margin-bottom: 20px;
    }
    .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .text-area {
        width: 400px;
        height: 200px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
        resize: none;
        box-sizing: border-box;
    }
    .title-text-area{
        width: 400px;
        height: 30px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    .post-button {
        background-color: #a4f3c4;
        color: #000000;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        margin-right: 10px;
    }
    
    .post-button:last-child {
        margin-right: 0;
    }
    .home-button {
            position: absolute;
            top: 10px;
            left: 10px;
    }

    .home-icon {
            width: 30px;
            height: 30px;
    }
    </style>
</head>
<script>
</script>
<body>
    <a href="main-page.php" class="home-button">
        <img src="images/home-icon.png" alt="Home" class="home-icon">
    </a>
    <div class="container">
            <h2>Edit Your Information</h2>
            <form action="update_user.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>"><br><br>

                <label for="username">User Name:</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>"><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>"><br><br>

                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo $user['phone_number']; ?>"><br><br>

                <button type="button" onclick="logout()" class="logout-button">
                    <img src="images/Logout.png" alt="Logout" style="width: 16px; height: 16px;">
                    <strong>Log out</strong>
                </button>
                <input type="hidden" name="update" value="1"> <!-- Add hidden field for update -->
                <input type="submit" value="Save Changes" class="post-button">
        </form>
    </div>

    <div style="padding: 20px;"></div>

    <div class="container">
        <h2>Change your password</h2>
        <form action="update_user.php" method="POST">
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password"><br><br>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password"><br><br>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password"><br><br>

            <button type="submit" name="change_password" class="post-button">Save Changes</button>
        </form>
    </div>
<script>
    function logout(){
    // Redirect the user to the login page
    window.location.href = "logout.php";
    }
</script>
</body>

</html>