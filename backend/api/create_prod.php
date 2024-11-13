<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require('../config.php');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name'], $data['quantity'], $data['productCode'], $data['location'], $data['qrCode'])) {
    $name = $data['name'];
    $quantity = $data['quantity'];
    $productCode = $data['productCode'];
    $location = $data['location'];
    $qrCode = base64_decode(explode(",", $data['qrCode'])[1]); // Decode base64 QR code
    
    $sql = "INSERT INTO product (name, quantity, productCode, location, qrCode) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $quantity, $productCode, $location, $qrCode);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product created successfully']);
    } else {
        echo json_encode(['message' => 'Failed to create product']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['message' => 'Missing required fields']);
}

$conn->close();
?>
