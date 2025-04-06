<?php
    session_start();

    if(isset($_SESSION['user_id'])){
        header("Location: home.php");
        exit();
    }

    include 'db.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ? AND password = ?");
            $stmt->execute([$username, $password]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: home.php");
                exit();
            } else {
                echo "<script>alert('Invalid username or password!');</script>";
            }
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
    <title>Login & Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
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
            background: url('img/login_bg.jpg') no-repeat center center/cover;
        }
        .login-container {
            position: fixed;
            top:50%;
            left: 40%;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            color: white;
            backdrop-filter: blur(10px);
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
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 12px;
            color: white;
            backdrop-filter: blur(10px);
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
        <h1 id="form-title">Login</h1><br>
        <form id="auth-form" method="POST" action="">
            <div class="input-box">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
        <br>
        <a class="toggle-link" href="register.php">Don't have an account? Register</a>
    </div>
</body>
</html>
