<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $db_host = 'localhost';
    $db_name = 'blog_website';
    $db_user = 'root';
    $db_password = '';

    try {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve blog ID from POST data
        $blog_id = $_POST['id'];
        $user_id = $_SESSION['user_id'];

        // Check if the logged-in user owns the blog
        $query = $db->prepare("SELECT * FROM blogs WHERE blog_id = :blog_id AND author_id = :user_id");
        $query->bindParam(':blog_id', $blog_id);
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        $blog = $query->fetch(PDO::FETCH_ASSOC);

        if ($blog) {
            // Delete comments associated with the blog
            $delete_comments_query = $db->prepare("DELETE FROM comments WHERE blog_id = :blog_id");
            $delete_comments_query->bindParam(':blog_id', $blog_id);
            $delete_comments_query->execute();

            // Delete the blog entry
            $delete_query = $db->prepare("DELETE FROM blogs WHERE blog_id = :blog_id");
            $delete_query->bindParam(':blog_id', $blog_id);
            $delete_query->execute();
            echo "success";
        } else {
            // The user does not have permission to delete this blog entry
            http_response_code(403); // Forbidden
            echo "You don't have permission to delete this blog entry.";
        }
    } catch(PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(400); // Bad Request
    echo "Invalid request.";
}
?>
