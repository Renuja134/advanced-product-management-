<?php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

// භාණ්ඩ ID එක URL එකෙන් ලබා ගැනීම
$product_id = $_GET['id'] ?? 0;

// භාණ්ඩයේ විස්තර ලබා ගැනීම
$stmt = $conn->prepare("SELECT 
                        p.*, 
                        c.name AS category_name,
                        u.full_name AS added_by
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.id
                        LEFT JOIN users u ON p.created_by = u.id
                        WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// භාණ්ඩය හමු නොවුනහොත්
if(!$product) {
    $_SESSION['error'] = 'භාණ්ඩය හමු නොවීය';
    header("Location: products.php");
    exit();
}
?>

<div class="container mt-4">
    <!-- ප්‍රදර්ශන පණිවුණ -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h4><?= htmlspecialchars($product['name']) ?> - විස්තර</h4>
        </div>
        
        <div class="card-body">
            <div class="row">
                <!-- භාණ්ඩ රූපය -->
                <div class="col-md-4">
                    <?php if($product['image_path'] && file_exists($product['image_path'])): ?>
                        <img src="<?= $product['image_path'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <div class="no-image bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <span class="text-muted">රූපය නොමැත</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- භාණ්ඩ විස්තර -->
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">භාණ්ඩ නම</th>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                        </tr>
                        <tr>
                            <th>කාණ්ඩය</th>
                            <td><?= htmlspecialchars($product['category_name']) ?></td>
                        </tr>
                        <tr>
                            <th>මිල</th>
                            <td>රු. <?= number_format($product['price'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>ප්‍රමාණය</th>
                            <td><?= $product['quantity'] ?></td>
                        </tr>
                        <tr>
                            <th>එකතු කළේ</th>
                            <td><?= htmlspecialchars($product['added_by']) ?> (<?= date('Y-m-d', strtotime($product['created_at'])) ?>)</td>
                        </tr>
                        <tr>
                            <th>තත්ත්වය</th>
                            <td>
                                <span class="badge <?= $product['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $product['status'] == 'active' ? 'සක්‍රිය' : 'අක්‍රිය' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- විස්තර -->
                    <div class="mt-3">
                        <h5>විස්තර:</h5>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    
                    <!-- ක්‍රියා බොත්තම් -->
                    <div class="mt-4">
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> ආපසු
                        </a>
                        
                        <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'): ?>
                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> සංස්කරණය
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once '../includes/footer.php';
?>
