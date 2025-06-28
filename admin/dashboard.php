<?php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// Dashboard statistics
$products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$sales = $conn->query("SELECT SUM(total_amount) FROM sales")->fetchColumn();
$lowStock = $conn->query("SELECT COUNT(*) FROM products WHERE quantity < 10")->fetchColumn();
?>

<div class="dashboard-container">
    <div class="stats-cards">
        <div class="card">
            <h3>මුළු භාණ්ඩ</h3>
            <p><?php echo $products; ?></p>
        </div>
        <div class="card">
            <h3>මුළු විකුණුම්</h3>
            <p><?php echo formatPrice($sales); ?></p>
        </div>
        <div class="card">
            <h3>අඩු ඉතිරි</h3>
            <p><?php echo $lowStock; ?></p>
        </div>
    </div>

    <!-- විකුණුම් ප්‍රස්තාරය -->
    <canvas id="salesChart"></canvas>
</div>

<?php require_once '../includes/footer.php'; ?>
