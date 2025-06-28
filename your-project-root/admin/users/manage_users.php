<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';

// පරිපාලක පමණක් භාවිතා කළ හැකි බව පරීක්ෂා කරන්න
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// පරිශීලක ලැයිස්තුව ලබා ගැනීම
$users = $conn->query("SELECT * FROM users ORDER BY role, username")->fetchAll();

// පරිශීලක මකාදැමීම
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    if ($user_id != $_SESSION['user_id']) { // ස්වයං ගිණුම මකන්න අවසර නැත
        $conn->exec("DELETE FROM users WHERE id = $user_id");
        header("Location: manage_users.php?success=User+deleted");
        exit();
    }
}
?>

<div class="container">
    <h2>පරිශීලක කළමනාකරණය</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    
    <a href="add_user.php" class="btn btn-primary mb-3">නව පරිශීලකයා</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>පරිශීලක නාමය</th>
                <th>නම</th>
                <th>භූමිකාව</th>
                <th>ක්‍රියා</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">සංස්කරණය</a>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('ඔබට මෙම පරිශීලකයා මැකීමට අවශ්‍යද?')">මකන්න</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../includes/footer.php'; ? 
