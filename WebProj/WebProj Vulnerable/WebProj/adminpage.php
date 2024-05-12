<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Abdulaziz">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index page</title>
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

        .index-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
        }

        .logo {
            width: 200px;
            margin-bottom: 20px;
        }

        .index-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .index-form label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333333;
        }

        .index-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .index-container button {
            width: 100%;
            padding: 10px;
            background-color: #82b5d8;
            color: #000000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'sixtyfourregular', sans-serif;
            box-shadow: #000000;
            text-shadow: #ccc;
        }

        .index-container button:hover {
            background-color: #508db9;
        }
    </style>

</head>
<body>
    <div class="index-container">
        <a href=main-page.php><img src="images/Blue.png" alt="Logo" class="logo"></a>
        <br>
        <a href="adminuser/adminuser.php">
            <button>
                <strong>Users</strong>
            </button>
        </a>
        <br>
        <br>
        <a href="adminblog/adminblog.php"> 
            <button>
                <strong>Blogs</strong>
            </button>
        </a>
    </div>
</body>
</html>