<?php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/header.php';

// පරිපාලක පමණක් භාවිතා කළ හැකි බව පරීක්ෂා කරන්න
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$settings_file = '../config/settings.php';
$settings = include $settings_file;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_settings = [
        'company_name' => $_POST['company_name'],
        'currency' => $_POST['currency'],
        'tax_rate' => (float)$_POST['tax_rate'],
        'pagination' => (int)$_POST['pagination'],
        'default_language' => $_POST['default_language']
    ];
    
    // සැකසුම් ගොනුව යාවත්කාලීන කිරීම
    $file_content = "<?php\nreturn " . var_export($new_settings, true) . ";\n";
    
    if (file_put_contents($settings_file, $file_content)) {
        $settings = $new_settings;
        $success = 'සැකසුම් සාර්ථකව යාවත්කාලීන කරන ලදී';
    } else {
        $error = 'සැකසුම් ගොනුව යාවත්කාලීන කිරීමට නොහැකි විය';
    }
}
?>

<div class="container">
    <h2>පද්ධති සැකසුම්</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>සමාගමේ නම</label>
            <input type="text" name="company_name" class="form-control" 
                   value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>මුදල් ඒකකය</label>
            <input type="text" name="currency" class="form-control" 
                   value="<?php echo htmlspecialchars($settings['currency']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>බදු අනුපාතය (%)</label>
            <input type="number" name="tax_rate" step="0.01" min="0" max="100" class="form-control" 
                   value="<?php echo $settings['tax_rate']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>පිටු බෙදාහැරීමේ අයිතම ගණන</label>
            <input type="number" name="pagination" min="5" max="100" class="form-control" 
                   value="<?php echo $settings['pagination']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>පෙරනිමි භාෂාව</label>
            <select name="default_language" class="form-control" required>
                <option value="si" <?= $settings['default_language'] == 'si' ? 'selected' : '' ?>>සිංහල</option>
                <option value="en" <?= $settings['default_language'] == 'en' ? 'selected' : '' ?>>English</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">සුරකින්න</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
