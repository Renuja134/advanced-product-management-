<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';

// පරිපාලක පමණක් ප්‍රවේශ විය හැකි
if ($_SESSION['role'] !== 'admin') {
    die("අවසර නැත");
}

$db = new Database();
$conn = $db->getConnection();

// පරාමිතීන් ලබා ගැනීම
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// විකුණුම් දත්ත ලබා ගැනීම
$sql = "SELECT s.sale_date, p.name as product_name, s.quantity, 
               s.unit_price, s.total_amount, s.customer_name
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE s.sale_date BETWEEN ? AND ?
        ORDER BY s.sale_date";

$stmt = $conn->prepare($sql);
$stmt->execute([$start_date, $end_date]);
$sales = $stmt->fetchAll();

// Excel ගොනුව සාදා ගැනීම
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="sales_report_'.date('Ymd').'.xls"');

echo "දිනය\tභාණ්ඩය\tප්‍රමාණය\tඒකක මිල\tමුළු මිල\tපාරිභෝගිකයා\n";

foreach ($sales as $sale) {
    echo date('Y-m-d', strtotime($sale['sale_date']))."\t";
    echo $sale['product_name']."\t";
    echo $sale['quantity']."\t";
    echo number_format($sale['unit_price'], 2)."\t";
    echo number_format($sale['total_amount'], 2)."\t";
    echo ($sale['customer_name'] ?? '-')."\n";
}
?>
