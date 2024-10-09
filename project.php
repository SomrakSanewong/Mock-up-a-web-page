<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $number = $_POST["number"];

    // Save the data to a file (you can modify this to save to a database)
    $file = fopen("data.txt", "a"); // Open or create a file for appending
    fwrite($file, "Name: $name, Email: $email, Number: $number\n");
    fclose($file);

    // Display a success message
    echo "บันทึกข้อมูลเรียบร้อยแล้ว<br>";
}

// Display all data
echo "<h2>บันทึกข้อมูลทั้งหมด</h2>";
$file = fopen("data.txt", "r");
if ($file) {
    while (!feof($file)) {
        $line = fgets($file);
        echo $line . "<><br></>\\";
    }
    fclose($file);
} else {
    echo "ยังไม่มีข้อมูลถูกบันทึก";
}
?><br>
<a href="http://localhost/%E0%B9%80%E0%B8%82%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B9%80%E0%B8%A7%E0%B9%89%E0%B8%9B/%E0%B9%82%E0%B8%9B%E0%B8%A3%E0%B9%80%E0%B8%88%E0%B8%84.php">หน้าหลัก</a>