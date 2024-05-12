<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['privileges'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Retrieve the user ID from the URL parameter
if (!isset($_GET['id'])) {
    header("Location: adminuser.php");
    exit();
}

$user_id = $_GET['id'];

// Function to fetch user by ID
function fetchUserById($user_id, $db) {
    try {
        // Prepare SQL statement to fetch user by ID
        $query = $db->prepare("SELECT * FROM blog_users WHERE user_id = :user_id");
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        
        // Fetch user data
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        return $user;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

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

// Fetch the user from the database
$user = fetchUserById($user_id, $db);

// Validate username and name for special characters
function validateInput($input) {
    // Allow only alphanumeric characters and spaces
    return preg_match('/^[a-zA-Z0-9\s]+$/', $input);
}

// Display the edit form with the fetched user data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            margin-bottom: 20px; /* Add some margin between containers */
        }
        .logo img {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .button-container {
            margin-top: 10px; /* Add margin above the buttons */
        }
        .Edit-button {
                background-color: #5ee7ff;
                color: #000000;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
                cursor: pointer;
                margin-right: 10px;
        }
        .Erase-button {
                background-color: #5ee7ff;
                color: #000000;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
                cursor: pointer;
                margin-right: 10px;
        }
        .blog-entry {
        margin-bottom: 10px;
        max-width: 600px;
        width: 100%;
        padding: 10px; 
        box-sizing: border-box;
        }

        tr:nth-child(even) {background-color: #f2f2f2;}

        .back-home-buttons {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .back-home-icon {
            width: 30px;
            height: 30px;
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
    </style>
</head>
<body>

    <div class="back-home-buttons">
    <a href="adminuser.php" class="home-button">
            <img src="../images/back.png" alt="Home" class="back-home-icon">
        </a>
        <a href="../main-page.php" class="home-button">
            <img src="../images/home-icon.png" alt="Home" class="back-home-icon">
        </a>
    </div>

    <div class="container">
    <h1>Edit User</h1>

    <form action="updateuser.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input-field" value="<?php echo $user['username']; ?>" pattern="[A-Za-z0-9\s]+" title="Username can only contain letters, numbers, and spaces" required>
        </div>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="input-field" value="<?php echo $user['name']; ?>" pattern="[A-Za-z\s]+" title="Name can only contain letters and spaces" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="input-field" value="<?php echo $user['email']; ?>">
        </div>
        <div>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" class="input-field" value="<?php echo $user['phone_number']; ?>">
        </div>
        <div>
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" class="input-field" value="<?php echo $user['date_of_birth']; ?>">
        </div>
        <div>
            <label for="privileges">Privileges:</label>
            <select id="privileges" name="privileges" class="input-field">
                <option value="admin" <?php if ($user['privileges'] === 'admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if ($user['privileges'] === 'user') echo 'selected'; ?>>User</option>
            </select>
        </div>
        <button type="submit" class="button ">Save Changes</button>
    </form>
    </div>
</body>
</html>
