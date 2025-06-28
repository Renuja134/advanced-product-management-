<?<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// දින පරාසය අනුව පිරික්සුම
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// විකුණුම් වාර්තාව ලබා ගැනීම
$sql = "SELECT s.*, p.name as product_name 
        FROM sales s 
        JOIN products p ON s.product_id = p.id
        WHERE s.sale_date BETWEEN ? AND ?
        ORDER BY s.sale_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$start_date, $end_date]);
$sales = $stmt->fetchAll();

// මුළු විකුණුම් ගණනය කිරීම
$total_sales = $conn->query(
    "SELECT SUM(total_amount) as total FROM sales 
     WHERE sale_date BETWEEN '$start_date' AND '$end_date'"
)->fetch()['total'];
?>

<div class="container">
    <h2>විකුණුම් වාර්තා</h2>
    
    <form method="get" class="mb-4 row">
        <div class="col-md-4">
            <label>ආරම්භක දිනය</label>
            <input type="date" name="start_date" class="form-control" 
                   value="<?php echo $start_date; ?>">
        </div>
        <div class="col-md-4">
            <label>අවසන් දිනය</label>
            <input type="date" name="end_date" class="form-control" 
                   value="<?php echo $end_date; ?>">
        </div>
        <div class="col-md-4">
            <label>&nbsp;</label><br>
            <button type="submit" class="btn btn-primary">පෙන්වන්න</button>
        </div>
    </form>
    
    <div class="alert alert-info">
        මුළු විකුණුම්: රු. <?php echo number_format($total_sales, 2); ?>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>දිනය</th>
                <th>භාණ්ඩය</th>
                <th>ප්‍රමාණය</th>
                <th>ඒකක මිල</th>
                <th>මුළු මිල</th>
                <th>පාරිභෝගිකයා</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($sale['sale_date'])); ?></td>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                <td><?php echo $sale['quantity']; ?></td>
                <td>රු. <?php echo number_format($sale['unit_price'], 2); ?></td>
                <td>රු. <?php echo number_format($sale['total_amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($sale['customer_name'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Excel ගොනුවට අපයාත කිරීම -->
    <a href="export_sales.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" 
       class="btn btn-success">
       Excel වාර්තාව බාගත කරන්න
    </a>
</div>

<?php require_once '../../includes/footer.php'; ?>
