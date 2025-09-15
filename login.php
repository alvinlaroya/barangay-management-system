<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $dbPassword = $user['password'];
        $valid = false;

        // ✅ Case 1: bcrypt hashed password
        if (password_verify($password, $dbPassword)) {
            $valid = true;

        // ✅ Case 2: old sha1 hashed password
        } elseif ($dbPassword === sha1($password)) {
            $valid = true;

            // 🔄 Upgrade hash to bcrypt automatically
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
        }

        if ($valid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['resident_id'] = $user['resident_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Fetch resident data and add to session
            $resident = null;
            if (!empty($user['resident_id'])) {
                $stmt_res = $conn->prepare("SELECT * FROM residents WHERE id = ? LIMIT 1");
                $stmt_res->bind_param("i", $user['resident_id']);
                $stmt_res->execute();
                $res_result = $stmt_res->get_result();
                if ($res_result->num_rows === 1) {
                    $resident = $res_result->fetch_assoc();
                    foreach ($resident as $key => $value) {
                        $_SESSION[$key] = $value;
                    }
                }
            }

            if($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: resident_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Barangay San Nicolas West</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, #ffffff20, transparent 70%);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-in-out;
        }

        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            display: block;
            margin: 0 auto 10px;
        }

        .login-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-label {
            font-size: 14px;
            color: #555;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #2575fc;
            border: none;
            border-radius: 10px;
            transition: 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a5fd6;
        }

        .register-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }

        .register-link:hover {
            color: #333;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="shape"></div>

<div class="login-container">
    <div class="login-card">
        <img src="assets/logo.png" alt="Barangay Logo" class="logo"> <!-- 🔁 Place your logo at assets/logo.png -->

        <h4 class="login-title">Barangay San Nicolas West</h4>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <a href="register_resident.php" class="register-link">Create a resident account</a>
    </div>
</div>

</body>
</html>
