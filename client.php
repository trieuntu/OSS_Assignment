<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câu hỏi Khảo sát</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<!--lay bien toan cuc-->
<?php
session_start();
if(isset($_POST["username"])and isset($_POST["password"])) {
    $user = $_POST["username"];
    $password = $_POST["password"];
    if(strlen($user)<7 or strlen($user)>9) header("location:./index.php");
    $_SESSION['user'] = $user;
    $_SESSION['password'] = $password;
}
?>
<div class="quiz-container">
    <h1>Bạn nghĩ mình có bảo mật thông tin cá nhân tốt không?</h1>
    <form class="quiz-form" method="get">
        <div class="input-group">
            <input type="radio" id="yes" name="answer" value="yes"
                <?php if(isset($_GET["press_submit"]) && $_GET["answer"]=="yes") echo "checked"?>>
            <label for="yes">Có</label>
        </div>
        <div class="input-group">
            <input type="radio" id="no" name="answer" value="no"
                <?php if(isset($_GET["press_submit"]) && $_GET["answer"]=="no") echo "checked"?>>
            <label for="no">Không</label>
        </div>
        <button type="submit" class="submit-button" name="press_submit" value="ok">Trả lời</button>
    </form>
</div>


<?php
    if (isset($_GET["press_submit"])) {
        echo "<div class=\"quiz-container\"> <h1 style='color: green'>Cảm ơn bạn đã tham gia khảo sát!!!</h1> </div>";
        $answer = $_GET["answer"];
        if(isset($_SESSION['user']) and isset($_SESSION['password'])){
            require("./config.php");
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Lỗi kết nối DB: " . $conn->connect_error);
            }
            mysqli_set_charset($conn, 'utf8');
            $timestamp = date("Y-m-d H:i:s", time()); // Format for DATETIME or TIMESTAMP
            $user = $_SESSION['user'];
            $password = $_SESSION['password'];
//            $query = "INSERT INTO `record`(`username`, `password`, `answer`, `created_at`) VALUES ('$user', '$password', '$answer', '$timestamp')";
//            $result=$conn->query($query);
//            if (!$result ) die ('<br> <b>Query failed</b>');
//            $conn->close();
//          ##Xử lý SQL Injection
            $query = "INSERT INTO `record`(`username`, `password`, `answer`, `created_at`) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ssss", $user, $password, $answer, $timestamp);
            $result = $stmt->execute();
            $stmt->close();
            $conn->close();
        }
    }
?>
</body>
</html>

