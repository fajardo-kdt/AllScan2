<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require('../config.php');

if (isset($_GET['productCode'])) {
    $productCode = $_GET['productCode'];
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['location'])) {
        $location = $data['location'];

        $sql = "UPDATE product SET location = ? WHERE productCode = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $location, $productCode);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Location updated successfully']);
            } else {
                echo json_encode(['message' => 'Product not found']);
            }
        } else {
            echo json_encode(['message' => 'Failed to update location']);
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'Location not provided']);
    }
} else {
    echo json_encode(['message' => 'Product code is required']);
}

$conn->close();
?>
