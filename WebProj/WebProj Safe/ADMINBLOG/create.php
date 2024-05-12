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

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['content'])) {
        // Insert the new blog entry into the database
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $author_id = $user['user_id']; // Use the logged-in user's ID as the author ID
        $created_at = date('Y-m-d H:i:s'); // Current timestamp

        $insert_query = $db->prepare("INSERT INTO blogs (title, content, author_id, created_at) VALUES (:title, :content, :author_id, :created_at)");
        $insert_query->bindParam(':title', $title);
        $insert_query->bindParam(':content', $content);
        $insert_query->bindParam(':author_id', $author_id);
        $insert_query->bindParam(':created_at', $created_at);
        $insert_query->execute();
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
<title>Blog Creation</title>
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
    .Erase-button
    {
        background-color: #e56e32;
        color: #000000;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        margin-right: 10px;

    }
    .Edit-button
    {
        background-color: #a0e532;
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
        <a href="adminblog.php" class="home-button">
            <img src="../images/back.png" alt="Home" class="back-home-icon">
        </a>
        <a href="../main-page.php" class="home-button">
            <img src="../images/home-icon.png" alt="Home" class="back-home-icon">
        </a>
    </div>
    <div class="container">
        <div class="logo">
            <img src="../images/Blue.png" alt="Logo">
        </div>
        <div class="title">Create</div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="title">Title</div>
            <textarea class="title-text-area" placeholder="Enter title here..." name="title" required></textarea>
            <div class="title">Blog Content</div>
            <textarea class="text-area" placeholder="Enter blog content here..." name="content" required></textarea>
            <div>
                <button class="post-button" type="submit" id="postButton">Post</button>
            </div>
        </form>
    </div>

<script>

    function postBlog() {
        var title = document.querySelector('.title-text-area').value;
        var content = document.querySelector('.text-area').value;
        // Perform post operation using AJAX or form submission
    }

    document.getElementById("postButton").addEventListener("click", function() {
        window.location.href = "../main-page.php";
    });

</script>
</body>
</html>