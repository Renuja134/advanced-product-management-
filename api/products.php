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
        <!-- භාණ්ඩ ලැයිස්තුවේ එක් එක් පේළිය තුළ -->
<td id="quantity-<?= $product['id'] ?>">
    <?= $product['quantity'] ?>
</td>
<td>
    <button class="btn btn-sm btn-success btn-increase" 
            data-id="<?= $product['id'] ?>" 
            data-amount="5">
        <i class="fas fa-plus"></i> 5
    </button>
    <button class="btn btn-sm btn-warning btn-decrease" 
            data-id="<?= $product['id'] ?>" 
            data-amount="1">
        <i class="fas fa-minus"></i> 1
    </button>
</td>
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
