<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

// භාණ්ඩ තොරතුරු ලබා ගැනීම
$product_id = $_GET['id'] ?? null;
$product = null;

if ($product_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$product) {
    header("Location: view.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // දත්ත ලබා ගැනීම
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $barcode = $_POST['barcode'];
    $status = $_POST['status'];

    // රූපය උඩුගත කිරීම
    $image_path = $product['image_path'];
    
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../../uploads/products/";
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        $allowTypes = array('jpg','png','jpeg','gif');
        
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // පැරණි රූපය මකන්න
                if (file_exists($product['image_path'])) {
                    unlink($product['image_path']);
                }
                $image_path = $targetFilePath;
            } else {
                $error = "රූපය උඩුගත කිරීමේ දෝෂයක්";
            }
        } else {
            $error = "JPG, JPEG, PNG, GIF ගොනු පමණක් අවසර ඇත";
        }
    }

    if (empty($error)) {
        $sql = "UPDATE products SET 
                name = ?, category = ?, description = ?, price = ?, 
                quantity = ?, barcode = ?, status = ?, image_path = ?, updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([
            $name, $category, $description, $price, 
            $quantity, $barcode, $status, $image_path, $product_id
        ])) {
            $success = "භාණ්ඩය සාර්ථකව යාවත්කාලීන කරන ලදී!";
        } else {
            $error = "යාවත්කාලීන කිරීමේ දෝෂය: " . implode(" ", $stmt->errorInfo());
        }
    }
}
?>

<div class="container mt-4">
    <h2>භාණ්ඩය සංස්කරණය කරන්න</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>නම</label>
            <input type="text" name="name" class="form-control" required 
                   value="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div class="form-group">
            <label>කාණ්ඩය</label>
            <select name="category" class="form-control" required>
                <option value="">තෝරන්න</option>
                <?php
                $categories = $conn->query("SELECT DISTINCT category FROM products")->fetchAll();
                foreach ($categories as $cat) {
                    $selected = ($cat['category'] == $product['category']) ? 'selected' : '';
                    echo "<option value='{$cat['category']}' $selected>{$cat['category']}</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- අනෙකුත් form fields -->
        
        <div class="form-group">
            <label>භාණ්ඩයේ රූපය</label>
            <input type="file" name="image" class="form-control-file">
            <?php if ($product['image_path']): ?>
                <img src="<?php echo $product['image_path']; ?>" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary">යාවත්කාලීන කරන්න</button>
        <a href="view.php" class="btn btn-secondary">අවලංගු කරන්න</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
