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

    $is_admin = false;
    if ($user['privileges'] === 'admin') {
        $is_admin = true;
    }

    // Fetch blog posts
    $blogQuery = $db->query("SELECT * FROM blogs");
    $blogPosts = $blogQuery->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <div class="sidebar-content">
                <div class="logo py-4">
                    <img src="images\Blue.png" width="140px" height="140px">
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

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="search-bar pt-3 pb-2 mb-3 border-bottom">
                <input class="form-control" type="text" placeholder="Search for blogs..." aria-label="Search">
            </div>

            <div id="blogPosts" class="row">
                <?php
                foreach ($blogPosts as $post) {
                    echo '<div class="col-12 blog-post">';
                    echo '<h2><a href="blogpost.php?blog_id=' . $post['blog_id'] . '">' . $post['title'] . '</a></h2>';
                    echo '<p>' . $post['content'] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.7.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function logout(){
    // Redirect the user to the login page
    window.location.href = "logout.php";
}

$(document).ready(function() {
    $('.search-bar input').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#blogPosts .blog-post").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>

</body>
</html>