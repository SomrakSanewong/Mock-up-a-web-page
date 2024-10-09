<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);
$categories = array(
    "เสื้อผ้า",
    "หนังสือ",
    "Toys",
    // เพิ่มประเภทสินค้าตามต้องการ
);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจำนวนสินค้าที่เพิ่มลงในตะกร้า
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// แสดงรายการเลือก
echo "<form method='post' action=''>";
echo "<label for='category'>เลือกประเภทสินค้า:</label>";
echo "<select name='category'>";
foreach ($categories as $category) {
    echo "<option value='$category'>$category</option>";
}
echo "</select>";
echo "<input type='submit' value='แสดงสินค้า'>";
echo "</form>";

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedCategory = $_POST['category'];

    // แสดงตารางสินค้า
    echo "<h2>$selectedCategory</h2>";
    echo "<table border='1'>";
    echo "<tr><th>สินค้า</th><th>รายละเอียด</th><th>ราคา</th><th>จำนวนคงเหลือ</th><th>ตะกร้าสินค้า</th><th>จำนวนที่เลือก</th></tr>";

    // ดึงข้อมูลจากฐานข้อมูล
    $sql = "SELECT * FROM products 
            JOIN categories ON products.category_id = categories.id
            WHERE categories.name = '$selectedCategory'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>$" . $row['price'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            
            // เพิ่มส่วนตะกร้าสินค้าและจำนวนที่เลือก
            echo "<td><button onclick='addToCart(" . $row['id'] . "," . $row['price'] . ")'>เพิ่มลงในตะกร้า</button></td>";
            echo "<td><input type='number' id='quantity_" . $row['id'] . "' value='1'></td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>ไม่พบสินค้าในประเภท $selectedCategory</td></tr>";
    }

    echo "</table>";

    // แสดงรายการสินค้าที่อยู่ในตะกร้า
   // ...

echo "<h3>ตะกร้าสินค้า</h3>";
echo "<ul id='cartList'>";
$totalQuantity = 0;
$totalPrice = 0;
foreach ($_SESSION['cart'] as $productId => $quantity) {
    $productSql = "SELECT * FROM products WHERE id = $productId";
    $productResult = $conn->query($productSql);
    if ($productResult->num_rows > 0) {
        $product = $productResult->fetch_assoc();
        echo "<li>" . $product['name'] . " x " . $quantity . " (ราคารวม: $" . ($product['price'] * $quantity) . ") 
        <a href='?remove=" . $product['id'] . "'>ลบ</a></li>";
        $totalQuantity += $quantity;
        $totalPrice += $product['price'] * $quantity;
    }
}
echo "</ul>";
echo "<p id='totalInfo'>จำนวนที่เลือก: " . $totalQuantity . ", ยอดรวมทั้งหมด: $" . $totalPrice . "</p>";

// เพิ่มส่วนรายละเอียดการชำระเงิน
echo "<h3>การชำระเงิน</h3>";
echo "<form method='post' action='payment_process.php'>";
echo "<label for='cardNumber'>หมายเลขบัตรเครดิต:</label>";
echo "<input type='text' id='cardNumber' name='cardNumber' required><br>";
echo "<label for='expiryDate'>วันที่หมดอายุ:</label>";
echo "<input type='text' id='expiryDate' name='expiryDate' placeholder='MM/YYYY' required><br>";
echo "<label for='cvv'>CVV:</label>";
echo "<input type='text' id='cvv' name='cvv' required><br>";
echo "<input type='submit' value='ชำระเงิน'>";
echo "</form>";

// ...

}

// ปิดการเชื่อมต่อ
$conn->close();
?>

<script>
function addToCart(productId, price) {
    var quantity = document.getElementById('quantity_' + productId).value;
    
    // เพิ่มสินค้าลงในตะกร้า
    if (quantity > 0) {
        if (!sessionStorage.getItem('cart')) {
            sessionStorage.setItem('cart', JSON.stringify({}));
        }

        var cart = JSON.parse(sessionStorage.getItem('cart'));
        cart[productId] = (cart[productId] || 0) + parseInt(quantity);
        sessionStorage.setItem('cart', JSON.stringify(cart));

        alert('เพิ่มสินค้าลงในตะกร้า: รหัสสินค้า ' + productId + ', จำนวน ' + quantity);
        updateCartInfo();
    } else {
        alert('กรุณาเลือกจำนวนที่มากกว่า 0');
    }
}

function updateCartInfo() {
    var cart = JSON.parse(sessionStorage.getItem('cart'));
    var totalQuantity = 0;
    var totalPrice = 0;

    for (var productId in cart) {
        var quantity = cart[productId];
        totalQuantity += quantity;

        // ดึงข้อมูลราคาจากฐานข้อมูล
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var product = JSON.parse(xhr.responseText);
                totalPrice += product.price * quantity;
                document.getElementById('totalInfo').innerHTML = "จำนวนที่เลือก: " + totalQuantity + ", ยอดรวมทั้งหมด: $" + totalPrice;
            }
        };

        xhr.open("GET", "get_product.php?id=" + productId, true);
        xhr.send();
    
    }
}
</script>
<a href="http://localhost/%E0%B9%80%E0%B8%82%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B9%80%E0%B8%A7%E0%B9%89%E0%B8%9B/%E0%B9%82%E0%B8%9B%E0%B8%A3%E0%B9%80%E0%B8%88%E0%B8%84.php">หน้าแรก</a>