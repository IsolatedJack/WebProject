<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['privileges'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Database connection parameters
$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

// Function to fetch all users from the database
function fetchAllUsers($db) {
    try {
        $query = $db->query("SELECT * FROM blog_users");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Connect to the database
try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Fetch all users from the database
$users = fetchAllUsers($db);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users</title>
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
    </style>
</head>
<body>
        
    <div class="back-home-buttons">
    <a href="../adminpage.php" class="home-button">
            <img src="../images/back.png" alt="Home" class="back-home-icon">
        </a>
        <a href="../main-page.php" class="home-button">
            <img src="../images/home-icon.png" alt="Home" class="back-home-icon">
        </a>
    </div>

    <div class="container">
    <h1>Users</h1>
        <table>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Date of Birth</th>
                <th>Date of Registration</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['phone_number']; ?></td>
                <td><?php echo $user['date_of_birth']; ?></td>
                <td><?php echo $user['date_of_registration']; ?></td>
                <td>
                    <a href="edituser.php?id=<?php echo $user['user_id']; ?>">Edit</a> |
                    <a href="deleteuser.php?id=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>
</body>
</html>
