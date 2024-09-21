<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="backend_view.css">
    <title>Admin Login</title>
</head>
<body>
<div class="login-container">
    <form method="POST" action="admin.php" class="login-form">
        <h2>Đăng nhập Quản trị viên</h2>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit" name="login">Đăng nhập</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</div>
</body>
</html>
