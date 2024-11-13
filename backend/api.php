<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require ('./config.php');

// Handle different request methods
$request_method = $_SERVER["REQUEST_METHOD"];

// Routing based on URL path
$request_uri = $_SERVER["REQUEST_URI"];
$path_parts = explode('/', $request_uri);

// Get all products
if ($request_method == 'GET' && count($path_parts) == 3 && $path_parts[2] == 'products') {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $products = [];
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode([]);
    }
}

// Get product by productCode
elseif ($request_method == 'GET' && count($path_parts) == 4 && $path_parts[2] == 'product') {
    $productCode = $path_parts[3];
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
}

// Create new product
elseif ($request_method == 'POST' && count($path_parts) == 3 && $path_parts[2] == 'create') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['name'], $data['quantity'], $data['productCode'], $data['location'], $data['qrCode'])) {
        echo json_encode(['message' => 'Missing required fields']);
        exit;
    }
    
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
}

// Update product location
elseif ($request_method == 'PUT' && count($path_parts) == 5 && $path_parts[2] == 'update-location') {
    $productCode = $path_parts[3];
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['location'])) {
        echo json_encode(['message' => 'Location not provided']);
        exit;
    }

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
    echo json_encode(['message' => 'Invalid request']);
}

$conn->close();
?>