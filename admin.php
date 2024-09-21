<!---- Chèn user admin mặc định-->
<!--INSERT INTO admin_user (username, password) VALUES ('admin', MD5('root'));-->
<?php
session_start();
require("./config.php");
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Lỗi kết nối DB: " . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8');

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM admin_user WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

if (!isLoggedIn()) {
    include 'login_view.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    if ($new_password === $confirm_password) {
        $conn->query("UPDATE admin_user SET password='$new_password' WHERE username='admin'");
        $success = "Đổi mật khẩu thành công!";
    } else {
        $error = "Xác nhận mật khẩu không khớp!";
    }
}

if (isset($_POST['delete_all'])) {
    $conn->query("DELETE FROM record");
    header("Location: admin.php");
    exit();
}

$order_by = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
$query = "SELECT * FROM record ORDER BY created_at $order_by";
$results = $conn->query($query);

$total_rows = $results->num_rows;
$limit = 50;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = "SELECT * FROM record ORDER BY created_at $order_by LIMIT $start, $limit";
$paged_results = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="backend_view.css">
    <title>Admin Dashboard</title>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <ul>
            <li><a href="#user" id="account-management-btn">Quản lý tài khoản</a></li>
            <li><a href="#view">Hiển thị nội dung</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <div class="user-info">
                <span>Chào, Admin</span>
                <a href="?logout=true">Đăng xuất</a>
            </div>
        </div>

        <div id="user" class="section hidden">
            <h2>Đổi mật khẩu</h2>
            <form method="POST">
                <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>
                <button type="submit" name="change_password">Đổi mật khẩu</button>
            </form>
            <?php if (isset($success)) echo "<p>$success</p>"; ?>
            <?php if (isset($error)) echo "<p>$error</p>"; ?>
        </div>

        <div id="view" class="section">
            <h2>Danh sách tài khoản trong record</h2>
            <table>
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Answer</th>
                    <th><a href="?order=<?php echo $order_by === 'ASC' ? 'desc' : 'asc'; ?>">Created At</a></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($paged_results->num_rows > 0): ?>
                    <?php $stt = $start + 1; while ($row = $paged_results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['password']; ?></td>
                            <td><?php echo $row['answer']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Không có dữ liệu</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <form method="POST">
                <button type="submit" name="delete_all" class="delete-btn">Xoá tất cả</button>
            </form>
        </div>
    </div>
</div>
<footer>
    <p> @Tin Học Đại Cương-NTU </p>
</footer>

<script>
    document.getElementById('account-management-btn').addEventListener('click', function() {
        var userSection = document.getElementById('user');
        if (userSection.classList.contains('hidden')) {
            userSection.classList.remove('hidden');
        } else {
            userSection.classList.add('hidden');
        }
    });
</script>

</body>
</html>
