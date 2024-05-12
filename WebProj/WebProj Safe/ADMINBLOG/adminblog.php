<?php
session_start();

$db_host = 'localhost';
$db_name = 'blog_website';
$db_user = 'root';
$db_password = '';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = $db->prepare("SELECT * FROM blogs WHERE author_id = :user_id");
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $blogs = $query->fetchAll(PDO::FETCH_ASSOC);
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


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="author" content="Abdulaziz">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Blog</title>
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
        <?php foreach ($blogs as $blog): ?>
            <div class="blog-entry" id="blog_<?php echo $blog['blog_id']; ?>">
                <h2><?php echo $blog['title']; ?></h2>
                <p><?php echo $blog['content']; ?></p>
                <div class="button-container">
                    <button class="Edit-button" onclick="editBlog(<?php echo $blog['blog_id']; ?>)">Edit</button>
                    <button class="Erase-button" onclick="deleteBlog(<?php echo $blog['blog_id']; ?>)">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="padding: 20px;">
        <div class="container">
            <button class="Edit-button" onclick="createBlog()"><Strong>Create A Blogpost</Strong></button>
        </div>
    </div>

<script>
    function editBlog(id) {
        // Redirect to the edit page with the blog ID
        window.location.href = "edit.php?id=" + id;
    }

    function deleteBlog(id) {
        if (confirm("Are you sure you want to delete this blog?")) {
            // Send an AJAX request to delete the blog
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_blog.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Blog deleted successfully, remove it from the UI
                        var blogElement = document.getElementById("blog_" + id);
                        blogElement.parentNode.removeChild(blogElement);
                        alert("Blog deleted successfully.");
                    } else {
                        // Error occurred while deleting the blog
                        alert("Error deleting the blog. Please try again later.");
                    }
                }
            };
            xhr.send("id=" + id);
        }
    }

    function createBlog() {
        // Redirect to the create page
        window.location.href = "create.php";
    }
</script>

</body>
</html>