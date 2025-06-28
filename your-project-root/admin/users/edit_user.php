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

$user_id = $_GET['id'] ?? null;
$error = '';
$success = '';

// පරිශීලක තොරතුරු ලබා ගැනීම
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    
    // පරිශීලක නාමය යාවත්කාලීන කිරීම
    if ($username !== $user['username']) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        
        if ($check->rowCount() > 0) {
            $error = 'පරිශීලක නාමය දැනටමත් භාවිතයේ ඇත';
        }
    }
    
    if (empty($error)) {
        $sql = "UPDATE users SET username = ?, full_name = ?, role = ?";
        $params = [$username, $full_name, $role];
        
        // මුරපදය යාවත්කාලීන කිරීම (එය සපයා ඇත්නම්)
        if (!empty($password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            $success = 'පරිශීලක තොරතුරු සාර්ථකව යාවත්කාලීන කරන ලදී';
            // නව තොරතුරු ලබා ගැනීම
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'යාවත්කාලීන කිරීමේ දෝෂය';
        }
    }
}
?>

<div class="container">
    <h2>පරිශීලකයා සංස්කරණය කරන්න</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>පරිශීලක නාමය</label>
            <input type="text" name="username" class="form-control" required 
                   value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        
        <div class="form-group">
            <label>සම්පූර්ණ නම</label>
            <input type="text" name="full_name" class="form-control" 
                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>භූමිකාව</label>
            <select name="role" class="form-control" required>
                <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>කාර්ය මණ්ඩලය</option>
                <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>මෙහෙයුම්කරු</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>පරිපාලක</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>නව මුරපදය (වෙනස් කිරීමට අවශ්‍ය නම් පමණක්)</label>
            <input type="password" name="password" class="form-control" placeholder="අලුත් මුරපදය">
        </div>
        
        <button type="submit" class="btn btn-primary">යාවත්කාලීන කරන්න</button>
        <a href="manage_users.php" class="btn btn-secondary">අවලංගු කරන්න</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
