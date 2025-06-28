<?php
require_once '../../config/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/header.php';

// පරිපාලක පමණක් ප්‍රවේශ විය හැකි
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $role = $_POST['role'];
    
    // වලංගු කිරීම්
    if (empty($username) || empty($password)) {
        $error = 'පරිශීලක නාමය සහ මුරපදය අත්‍යවශ්‍යයි';
    } else {
        // පරිශීලක නාමය භාවිතයේ තිබේදැයි පරීක්ෂා කරන්න
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'පරිශීලක නාමය දැනටමත් භාවිතයේ ඇත';
        } else {
            // මුරපදය රහස් කිරීම
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$username, $hashed_password, $full_name, $role])) {
                $success = 'පරිශීලකයා සාර්ථකව එකතු කරන ලදී';
                $_POST = []; // ආකෘතිය හිස් කරන්න
            } else {
                $error = 'දත්ත ගබඩා කිරීමේ දෝෂය';
            }
        }
    }
}
?>

<div class="container">
    <h2>නව පරිශීලකයා</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>පරිශීලක නාමය *</label>
            <input type="text" name="username" class="form-control" required 
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>මුරපදය *</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>සම්පූර්ණ නම</label>
            <input type="text" name="full_name" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>භූමිකාව *</label>
            <select name="role" class="form-control" required>
                <option value="staff" <?php echo ($_POST['role'] ?? '') == 'staff' ? 'selected' : ''; ?>>කාර්ය මණ්ඩලය</option>
                <option value="manager" <?php echo ($_POST['role'] ?? '') == 'manager' ? 'selected' : ''; ?>>මෙහෙයුම්කරු</option>
                <option value="admin" <?php echo ($_POST['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>පරිපාලක</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">සුරකින්න</button>
        <a href="manage_users.php" class="btn btn-secondary">අවලංගු කරන්න</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
