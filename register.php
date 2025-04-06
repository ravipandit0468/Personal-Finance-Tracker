<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION['userdata'])){
        header("Location: home.php");
    }
?>


<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        header("Location: login.php");
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>Login & Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            justify-content: center;
            height: 100vh;
            background:  url('img/login_bg.jpg') no-repeat center center/cover;
        }
        .login-container {
            position: fixed;
            top:50%;
            left: 40%;
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            color: white;
            backdrop-filter: blur(10px); /* Glassmorphism effect */
        }
        .input-box {
            margin: 10px 0;
            width: 100%;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .toggle-link {
            margin-top: 15px;
            cursor: pointer;
            color: #667eea;
        }
        .login-header {
            position: fixed;
            top:30%;
            left: 20%;
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white */
            padding: 40px;
            border-radius: 12px;
            color: white;
            backdrop-filter: blur(10px); /* Glassmorphism effect */
        }

        .login-header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .login-header h2 {
            font-size: 1.2rem;
            font-weight: 300;
            opacity: 0.9;
        }

    </style>
</head>
<body>
    <div class="login-header">
        <h1>Welcome to Finance Tracker</h1>
        <h2>Manage your income and expenses easily with our simple finance tracking system</h2>
    </div>

    <div class="login-container">
        <h1 id="form-title">Register</h1><br>
        <form id="auth-form" method="POST" action="register.php">
            <div class="input-box">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-box" id="email-box">
                <input type="email" id="email" name="email" placeholder="Email">
            </div>
            <button type="submit" class="btn">Submit</button>
        </form><br>
        <a href="login.php" class="toggle-link">Already have an account? Login</a>
    </div>
</body>
</html>