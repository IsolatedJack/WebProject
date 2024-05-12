<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the blog ID from the URL parameter
if (!isset($_GET['id'])) {
    header("Location: adminblog.php");
    exit();
}

$blog_id = $_GET['id'];

// Function to fetch blog by ID
function fetchBlogById($blog_id, $db) {
    try {
        // Prepare SQL statement to fetch blog by ID
        $query = $db->prepare("SELECT * FROM blogs WHERE blog_id = :blog_id");
        $query->bindParam(':blog_id', $blog_id);
        $query->execute();
        
        // Fetch blog data
        $blog = $query->fetch(PDO::FETCH_ASSOC);
        
        return $blog;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Connect to the database
$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Fetch the blog entry from the database
$blog = fetchBlogById($blog_id, $db);

// Display the edit form with the fetched blog data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
</head>
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
    .blog-entry {
        margin-bottom: 10px; /* Add some margin between blog entries */
    }
    .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
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
<body>

    <div class="back-home-buttons">
        <a href="adminblog.php" class="home-button">
            <img src="../images/back.png" alt="Home" class="back-home-icon">
        </a>
        <a href="../main-page.php" class="home-button">
            <img src="../images/home-icon.png" alt="Home" class="back-home-icon">
        </a>
    </div>
    <div class="container">
        <!-- Edit form goes here -->
        <h2>Edit Blog</h2>
        <form action="update_blog.php" method="post">
            <input type="hidden" name="blog_id" value="<?php echo $blog['blog_id']; ?>">
            <div class="title">Title</div>
            <input type="text" id="title" name="title" class="title-text-area"value="<?php echo $blog['title']; ?>"><br>
            <div class="title">Blog Content</div>
            <textarea id="content" name="content" class="text-area"><?php echo $blog['content']; ?></textarea><br>
            <button type="submit" class="Edit-button">Save Changes</button>
        </form>
    </div>
</body>
</html>
