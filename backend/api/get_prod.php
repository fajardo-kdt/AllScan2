<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require('../config.php');

if (isset($_GET['productCode'])) {
    $productCode = $_GET['productCode'];
    $sql = "SELECT * FROM product WHERE productCode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if (!empty($product['qrCode'])) {
            $product['qrCode'] = base64_encode($product['qrCode']);
        }
        echo json_encode($product);
    } else {
        echo json_encode(['message' => 'Product not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['message' => 'Product code is required']);
}

$conn->close();
?>
