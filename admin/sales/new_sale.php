<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $customer_name = $_POST['customer_name'] ?? '';
    
    // භාණ්ඩ තොරතුරු ලබා ගැනීම
    $product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch();
    
    if ($product && $product['quantity'] >= $quantity) {
        $unit_price = $product['price'];
        $total_amount = $unit_price * $quantity;
        
        // වෙළඳාම් ගබඩා කිරීම
        $sql = "INSERT INTO sales (product_id, quantity, unit_price, total_amount, customer_name) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$product_id, $quantity, $unit_price, $total_amount, $customer_name])) {
            // ඉතිරි ප්‍රමාණය යාවත්කාලීන කිරීම
            $new_quantity = $product['quantity'] - $quantity;
            $conn->exec("UPDATE products SET quantity = $new_quantity WHERE id = $product_id");
            
            $success = "විකුණුම සාර්ථකව ලියාපදිංචි කරන ලදී!";
        } else {
            $error = "විකුණුම් ලියාපදිංචි කිරීමේ දෝෂය";
        }
    } else {
        $error = "ප්‍රමාණවත් භාණ්ඩ ඉතිරි නොමැත හෝ භාණ්ඩය හමු නොවීය";
    }
}

// භාණ්ඩ ලැයිස්තුව ලබා ගැනීම
$products = $conn->query("SELECT id, name, price, quantity FROM products WHERE status='active' AND quantity > 0")->fetchAll();
?>

<div class="container mt-4">
    <h2>නව විකුණුම්</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>භාණ්ඩය</label>
            <select name="product_id" class="form-control" required>
                <option value="">තෝරන්න</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?> 
                        (රු. <?php echo number_format($product['price'], 2); ?>, 
                        ඉතිරි: <?php echo $product['quantity']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>ප්‍රමාණය</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>
        
        <div class="form-group">
            <label>පාරිභෝගික නම (විකල්ප)</label>
            <input type="text" name="customer_name" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">ලියාපදිංචි කරන්න</button>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
