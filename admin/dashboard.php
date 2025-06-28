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
<div class="dashboard-container">
    <!-- Real-time Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card bg-primary">
                <h3>Total Products</h3>
                <p id="productCount"><?= $totalProducts ?></p>
                <i class="fas fa-box"></i>
            </div>
        </div>
        <!-- Add 3 more stat cards -->
    </div>

    <!-- Recent Activity Feed -->
    <div class="activity-feed mt-4">
        <h4>Recent Activity</h4>
        <ul class="list-group">
            <?php foreach($recentActivities as $activity): ?>
            <li class="list-group-item">
                <span class="badge bg-<?= $activity['type'] ?>"><?= $activity['time'] ?></span>
                <?= $activity['message'] ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Inventory Alert -->
    <div class="alert alert-warning mt-4" id="lowStockAlert" style="display:none">
        <i class="fas fa-exclamation-triangle"></i>
        <span id="lowStockCount"></span> items are low in stock!
    </div>
</div>

<script>
// AJAX for real-time updates
setInterval(function(){
    $.get('api/get_stats.php', function(data){
        $('#productCount').text(data.products);
        $('#salesCount').text(data.sales);
        
        if(data.low_stock > 0){
            $('#lowStockAlert').show();
            $('#lowStockCount').text(data.low_stock);
        }
    });
}, 30000); // Update every 30 seconds
        </script>
