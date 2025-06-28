<?php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// පිටු බෙදාහැරීම සහ පිරික්සුම
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// SQL query
$sql = "SELECT * FROM products WHERE status='active'";
$countSql = "SELECT COUNT(*) FROM products WHERE status='active'";

if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $countSql .= " AND (name LIKE :search OR description LIKE :search)";
}

if (!empty($category)) {
    $sql .= " AND category = :category";
    $countSql .= " AND category = :category";
}

$sql .= " LIMIT :limit OFFSET :offset";

// දත්ත ලබා ගැනීම
$stmt = $conn->prepare($sql);
$countStmt = $conn->prepare($countSql);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm);
    $countStmt->bindParam(':search', $searchTerm);
}

if (!empty($category)) {
    $stmt->bindParam(':category', $category);
    $countStmt->bindParam(':category', $category);
}

$stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$countStmt->execute();
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);
?>

<div class="container">
    <h2>භාණ්ඩ බැලීම</h2>
    
    <!-- පිරික්සුම් ආකෘතිය -->
    <form method="get" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="සොයන්න..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-control">
                    <option value="">සියලුම කාණ්ඩ</option>
                    <?php
                    $categories = $conn->query("SELECT DISTINCT category FROM products")->fetchAll();
                    foreach ($categories as $cat) {
                        $selected = ($category == $cat['category']) ? 'selected' : '';
                        echo "<option value='{$cat['category']}' $selected>{$cat['category']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">පිරික්සන්න</button>
            </div>
        </div>
    </form>

    <!-- භාණ්ඩ වගුව -->
    <table class="table table-bordered table-striped" id="productsTable">
        <thead>
            <tr>
                <th>නම</th>
                <th>කාණ්ඩය</th>
                <th>මිල</th>
                <th>ප්‍රමාණය</th>
                <th>ක්‍රියා</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td><?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">සංස්කරණය</a>
                    <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn
