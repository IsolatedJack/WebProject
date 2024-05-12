<?php
session_start(); // Start session

$alert = ""; // Initialize alert variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "blog_website";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = strtolower($_POST['username']);
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $name = $_POST['name'];
    $email = strtolower($_POST['email']);
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $privileges = "user";
    $date_of_registration = date("Y-m-d");

    // Regular expression pattern to allow only alphanumeric characters and spaces
    $pattern = '/^[a-zA-Z0-9\s]+$/';

    if (empty($username) || empty($password) || empty($name) || empty($email) || empty($phone_number) || empty($date_of_birth)) {
        $alert = "All fields are required.";
    } elseif (!preg_match($pattern, $username) || !preg_match($pattern, $name)) {
        $alert = "Username and name should only contain letters, numbers, and spaces.";
    } else {
        // Check if the age is at least 18
        $dob = new DateTime($date_of_birth);
        $now = new DateTime();
        $age = $dob->diff($now)->y;

        if ($age < 18) {
            $alert = "You must be at least 18 years old to create an account.";
        } else {
            // Check if the username already exists
            $stmt = $conn->prepare("SELECT * FROM blog_users WHERE LOWER(username) = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $alert = "Username already exists.";
            } else {
                // Check if the email already exists
                $stmt = $conn->prepare("SELECT * FROM blog_users WHERE LOWER(email) = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                if ($result->num_rows > 0) {
                    $alert = "Email already exists.";
                } else {
                    // Check if the phone number already exists
                    $stmt = $conn->prepare("SELECT * FROM blog_users WHERE phone_number = ?");
                    $stmt->bind_param("s", $phone_number);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    if ($result->num_rows > 0) {
                        $alert = "Phone number already exists.";
                    } else {
                        // Insert new user into database
                        $stmt = $conn->prepare("INSERT INTO blog_users (username, password, name, email, phone_number, date_of_birth, privileges, date_of_registration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssssss", $username, $hashed_password, $name, $email, $phone_number, $date_of_birth, $privileges, $date_of_registration);

                        if ($stmt->execute()) {
                            echo "<script>alert('Account created successfully'); window.location.href='login.php'</script>";
                            exit;
                        } else {
                            $alert = "Failed to create account. Please try again later.";
                        }

                        $stmt->close();
                    }
                }
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Creation</title>
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

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px;
        background-color: #ffffff;
        border-radius: 14px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        align-items: center;
    }


    .logo img {
        max-width: 200px;
    }

    .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .input-field {
        width: 200px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }

    .button {
        background-color: #a4f3c4;
        color: #000000;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 20px;
    }

    .button:last-child {
        margin-bottom: 0;
    }

    .tooltip {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 200px;
        background-color: #000000;
        color: #ffffff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    /* Updated CSS for the phone container */

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
    </style>
    <script>
        function PasswordValidate() {
            var password_1 = document.getElementById("password1").value;
            var password_2 = document.getElementById("password2").value;
            if (password_1 !== password_2) {
                alert("Passwords must match.");
                return false;
            } else {
                return true;
            }
        }
    </script>
</head>

<body>

<a href="index2.php" class="home-button">
        <img src="images/home-icon.png" alt="Home" class="home-icon">
    </a>
<div class="container">
    <div class="logo">
        <img src="images/Blue.png" alt="Logo">
    </div>
    
    <form method="post" action="" class="container">
        <div class="title">Create Your Account</div>
        <?php if (!empty($alert)) : ?>
            <div class="alert"><?php echo $alert; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)) : ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Username" class="input-field" required>
        <div class="tooltip">
            <input type="password" name="password" placeholder="Password" id="password1" class="input-field" required>
            <span class="tooltiptext">Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character.</span>
        </div>
        <input type="password" placeholder="Confirm Password" id="password2" class="input-field" required>
        <input type="text" name="name" placeholder="Name" class="input-field" required>
        <input type="email" name="email" id="email" placeholder="Email" class="input-field" required>

        <div class="phone-container">
            <input type="text" name="phone_number" placeholder="Phone Number" class="input-field phone-number" required>
        </div>
        <div class="tooltip">
        <input type="date" name="date_of_birth" placeholder="Date of Birth" class="input-field" required>
        <span class="tooltiptext">Must be atleast 18 years old.</span>
        </div>

        <input type="submit" name="submit" value="Create Account" class="button" onclick="return PasswordValidate()">
        <a href="login.php py-4">
            <button class="button">Already have an account?</button>
        </a>
    </form>
</div>
</body>
</html>