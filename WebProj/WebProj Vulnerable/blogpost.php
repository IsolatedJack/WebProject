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

        // Fetch blog posts
        $blogQuery = $db->query("SELECT * FROM blogs");
        $blogPosts = $blogQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    $is_admin = false;
    if ($user['privileges'] === 'admin') {
        $is_admin = true;
    }

    // Process comment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $commentContent = $_POST['comment'];
        $blog_id = $_POST['blog_id'];
        $user_id = $user['user_id']; // Assuming user_id is the primary key in the blog_users table
        
        $commentContent = htmlspecialchars($commentContent, ENT_QUOTES, 'UTF-8');

        $insertQuery = $db->prepare("INSERT INTO comments (blog_id, user_id, content, created_at) VALUES (:blog_id, :user_id, :content, NOW())");
        $insertQuery->bindParam(':blog_id', $blog_id);
        $insertQuery->bindParam(':user_id', $user_id);
        $insertQuery->bindParam(':content', $commentContent);
        $insertQuery->execute();

        // Refresh the page to show the newly added comment
        header("Location: blogpost.php?blog_id=$blog_id");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: black !important;
        }
        .sidebar {
            /* Animated gradient background */
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            font-family: Arial, sans-serif;
            border-radius: 0 20px 20px 0;
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
        .sidebar .logo,
        .sidebar .user-info{
            padding: 10px;
            margin-bottom: 10px;
        }
        .sidebar .logout {
            background-color: red;
            text-align: center;
            cursor: pointer;
        }
        .sidebar .logout {
            text-align: center;
            cursor: pointer;
        }
        .search-bar input {
            margin-bottom: 20px;
        }
        .blog-post {
            background-color: white;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 15px;
            cursor: pointer;
        }

        .admin-button {
            color: #000000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .user-info {
            background-color: white;
            border-width: 4px 4px 4px 0; /* Set border width for top, right, bottom, and left */
            border-style: solid; /* Set border style */
            border-color: #ddd; /* Set border color */
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 0 10px 10px 0;
            max-width: 300px;
            color: black;
        }
        .sidebar-content {
            background-color: white;
            border-width: 4px 4px 4px 0; /* Set border width for top, right, bottom, and left */
            border-style: solid; /* Set border style */
            border-color: #ddd; /* Set border color */
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 0 20px 20px 0;
            max-width: 300px;
            color: rgb(135, 127, 127);
            padding-bottom: 10px;
        }

        .comment-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <div class="sidebar-content">
                    <div class="logo py-4">
                        <a href="main-page.php"><img src="images/Blue.png" width="140px" height="140px" alt="logo"></a>
                    </div>
                    <div class="user-info">
                        <p><Strong>User Info</Strong></p>
                        <p><Strong>Welcome, <?php echo $user['name']; ?></Strong></p>
                        <p><Strong>Email: </Strong><?php echo $user['email']; ?></p>
                    </div>
                    <?php if ($is_admin): ?>
                    <!-- Display the "Admin" button if the user has admin privileges -->
                    <div class="admin-button py-4">
                        <a href="adminpage.php" class="btn btn-primary">Admin Tools</a>
                    </div>
                    <?php endif; ?>
                    <div class="admin-button py-4">
                        <a href="useraccount.php" class="btn btn-primary">User Info</a>
                    </div>
                    <div class="logout btn btn-primary" onclick="logout()">
                        Log out
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
        <div style="padding: 20px;">
        </div>

            <!-- Blog content -->
            <div class="blog-content">
                <?php
                try {
                    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Retrieve blog content from the database
                    $blog_id = $_GET['blog_id'];

                    $query = $db->prepare("SELECT title, content FROM blogs WHERE blog_id = :blog_id"); // Change the query to retrieve the desired blog post
                    $query->bindParam(':blog_id', $blog_id);
                    $query->execute();
                    $blog = $query->fetch(PDO::FETCH_ASSOC);

                    // Display the blog content
                    echo '<br>';
                    echo '<h2>' . $blog['title'] . '</h2>';
                    echo '<p>' . $blog['content'] . '</p>';

                    // Retrieve comments related to the blog from the database
                    $commentsQuery = $db->prepare("SELECT c.content, c.created_at, u.name FROM comments c INNER JOIN blog_users u ON c.user_id = u.user_id WHERE c.blog_id = :blog_id");
                    $commentsQuery->bindParam(':blog_id', $blog_id);
                    $commentsQuery->execute();
                    $comments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($comments)) {
                        echo '<h3>Comments:</h3>';
                        foreach ($comments as $comment) {
                            // Function to convert URLs into clickable links
                            $commentContent = preg_replace('/(https?:\/\/\S+)/', '<a href="$1" target="_blank">$1</a>', $comment['content']);
                    
                            echo '<div class="comment-box">';
                            echo '<p>' . $commentContent . '</p>';
                            echo '<p><strong>Posted by: ' . $comment['name'] . '</strong></p>';
                            echo '<p><em>Posted on: ' . $comment['created_at'] . '</em></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No comments yet.</p>';
                    }
                    
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>

            <!-- Comment form -->
            <?php if (isset($_SESSION['username'])): ?>
                <div class="comment-form">
                    <h3>Add a Comment</h3>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                        <div class="form-group">
                            <label for="comment" class="sr-only">Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Write your comment here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            <?php else: ?>
                <p>Please <a href="login.php">log in</a> to add a comment.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Logout function
function logout() {
    window.location = 'logout.php';
}
</script>

</body>
</html>