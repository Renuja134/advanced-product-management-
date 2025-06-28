<?php
header('Content-Type: application/json');
require '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        // Get product list
        $stmt = $conn->query("SELECT * FROM products");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'POST':
        // Create new product
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $conn->prepare("INSERT INTO products (...) VALUES (...)");
        $stmt->execute([...]);
        echo json_encode(['status' => 'success']);
        break;
        
    // Other methods...
}
?>
