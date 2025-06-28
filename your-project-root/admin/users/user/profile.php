<?php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// පරිශීලක තොරතුරු ලබා ගැනීම
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // මුරපදය යාවත්කාලීන කිරීම
    if (!empty($new_password)) {
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name = ?, password = ? WHERE id = ?";
            $params = [$full_name, $hashed_password, $user_id];
        } else {
            $error = 'වත්මන් මුරපදය වැරදියි';
        }
    } else {
        $sql = "UPDATE users SET full_name = ? WHERE id = ?";
        $params = [$full_name, $user_id];
    }
    
    if (empty($error)) {
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            $success = 'පැතිකණ්ඩ යාවත්කාලීන කරන ලදී';
            // නව තොරතුරු ලබා ගැනීම
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // සැසිය යාවත්කාලීන කරන්න
            $_SESSION['full_name'] = $user['full_name'];
        } else {
            $error = 'යාවත්කාලීන කිරීමේ දෝෂය';
        }
    }
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>මගේ පැතිකණ්ඩ</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">පරිශීලක නාමය</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">සම්පූර්ණ නම</label>
                    <div class="col-sm-9">
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">භූමිකාව</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="<?php echo $user['role']; ?>" readonly>
                    </div>
                </div>
                
                <hr>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">වත්මන් මුරපදය</label>
                    <div class="col-sm-9">
                        <input type="password" name="current_password" class="form-control" placeholder="මුරපදය වෙනස් කිරීමට අවශ්‍ය නම් පමණක්">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">නව මුරපදය</label>
                    <div class="col-sm-9">
                        <input type="password" name="new_password" class="form-control" placeholder="නව මුරපදය ඇතුළත් කරන්න">
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> යාවත්කාලීන කරන්න
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
require_once '../includes/footer.php';
?>
