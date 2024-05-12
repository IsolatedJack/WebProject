<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "blog_website";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $email = mysqli_real_escape_string($conn, $email); // Sanitize email input
    $pass = mysqli_real_escape_string($conn, $pass); // Sanitize password input

    if (empty($email)) {
        header("Location: login.php?error=Email is required");
        exit();
    } elseif (empty($pass)) {
        header("Location: login.php?error=Password is required");
        exit();
    } else {
        $stmt = $conn->prepare("SELECT * FROM blog_users WHERE LOWER(email) = ?");
        $emailLower = strtolower($email);
        $stmt->bind_param("s", $emailLower);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                // Use htmlspecialchars to escape special characters in JavaScript strings
                $username = htmlspecialchars($row['username']);
                echo "<script>alert('Logged in as $username!');</script>";
                $_SESSION['username'] = $username;
                $_SESSION['name'] = htmlspecialchars($row['name']);
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['privileges'] = $row['privileges'];
                header("Location: main-page.php");
                exit();
            } else {
                echo "<script>alert('Incorrect email or password!');</script>";
            }
        } else {
            echo "<script>alert('Incorrect email or password!');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Abdulaziz">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }

        .logo {
            width: 200px;
            margin-bottom: 20px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-form label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333333;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #82b5d8;
            color: #000000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'sixtyfourregular', sans-serif;
            box-shadow: #000000;
            text-shadow: #ccc;
        }

        .login-container button:hover {
            background-color: #508db9;
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
    <script>
        // Your JavaScript code here
    </script>
</head>
<body>

<a href="index2.php" class="home-button">
        <img src="images/home-icon.png" alt="Home" class="home-icon">
    </a>
    <div class="login-container">
        <img src="images/Blue.png" alt="Logo" class="logo">
        
        <form class="login-form" action="" method="POST">
            <?php if (isset($_GET['error'])) { ?>
                <p><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <p><?php echo $_GET['success']; ?></p>
            <?php } ?>
            <label for="email"><strong>Email</strong></label>
            <input type="text" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="password"><strong>Password</strong></label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            
            <button type="submit"><strong>Log in</strong>
                <img src="images/Login.png" alt="Login Icon" style="width: 20px; vertical-align: middle; margin-left: 5px;">
            </button>
            
        </form>
        <br>
        <a href="create_account.php">
            <button>
                <strong>Create Account</strong>
            </button>
        </a>
    </div>
</body>
</html>