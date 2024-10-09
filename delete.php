<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mytable";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM mytable WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "ลบข้อมูลเรียบร้อยแล้ว<br>";
    } else {
        echo "การลบข้อมูลล้มเหลว: " . $conn->error;
    }
}

$conn->close();
?>
<a href="http://localhost/%E0%B9%80%E0%B8%82%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B9%80%E0%B8%A7%E0%B9%89%E0%B8%9B/%E0%B9%82%E0%B8%9B%E0%B8%A3%E0%B9%80%E0%B8%88%E0%B8%84.php">กลับไปที่หน้าหลัก</a>