<?php
require_once __DIR__ . '/functions.php';
start_secure_session();

if (!empty($_SESSION['admin_username'])) {
    header('Location: dashboard.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token(array_value($_POST, 'csrf_token'))) {
        http_response_code(400);
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } else {
        $username = trim((string) array_value($_POST, 'username', ''));
        $password = (string) array_value($_POST, 'password', '');

        $stmt = $conn->prepare('SELECT username, password FROM admin WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_username'] = $row['username'];
                header('Location: dashboard.php');
                exit;
            }
        }

        $error = 'Username atau kata sandi salah.';
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Disdukcapil</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 350px;
            text-align: center;
            border: 1px solid #ddd;
        }

        h2 {
            margin-bottom: 20px;
            color: #3498db;
            font-size: 24px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            outline: none;
        }

        input:focus {
            border-color: #3498db;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #aaa;
        }

        @media (max-width: 600px) {
            .login-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login Admin</h2>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

</div>

</body>
</html>
