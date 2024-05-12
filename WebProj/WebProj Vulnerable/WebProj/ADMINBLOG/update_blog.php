<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    if (isset($_POST['blog_id']) && isset($_POST['title']) && isset($_POST['content'])) {
        // Retrieve form data
        $blog_id = $_POST['blog_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        // Update blog entry in the database (you need to implement this function)
        updateBlog($blog_id, $title, $content);

        // Redirect back to the admin blog page after updating
        header("Location: adminblog.php");
        exit();
    } else {
        // Handle invalid form data
        echo "Invalid form data.";
        exit();
    }
} else {
    // Redirect to the edit page if form data is not submitted via POST method
    header("Location: edit.php");
    exit();
}

// Function to update blog entry in the database
function updateBlog($blog_id, $title, $content) {
    // Connect to the database
    $db_host = 'localhost';
    $db_name = 'blog_website';
    $db_user = 'root';
    $db_password = '';

    try {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL statement to update blog entry
        $query = $db->prepare("UPDATE blogs SET title = :title, content = :content WHERE blog_id = :blog_id");
        $query->bindParam(':title', $title);
        $query->bindParam(':content', $content);
        $query->bindParam(':blog_id', $blog_id);
        $query->execute();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
