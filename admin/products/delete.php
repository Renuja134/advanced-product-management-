
<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

// පරිපාලක/මෙහෙයුම්කරු පමණක් භාවිතා කළ හැකි බව පරීක්ෂා කරන්න
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$product_id = $_GET['id'] ?? null;

if ($product_id) {
    try {
        // භාණ්ඩයේ රූපය මකා දැමීම
        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }
        
        // දත්ත ගබඩාවෙන් භාණ්ඩය මකා දැමීම
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        header("Location: ../products/view.php?success=Product+deleted");
    } catch (PDOException $e) {
        header("Location: ../products/view.php?error=Delete+failed");
    }
} else {
    header("Location: ../products/view.php");
}
exit();
?>
