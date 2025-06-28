
<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_login'] = date('Y-m-d H:i:s');
            
            // අවසන් ලොගින් වේලාව යාවත්කාලීන කිරීම
            $conn->exec("UPDATE users SET last_login = NOW() WHERE id = ".$user['id']);
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'වැරදි පරිශීලක නාමය හෝ මුරපදය';
        }
    } else {
        $error = 'වැරදි පරිශීලක නාමය හෝ මුරපදය';
    }
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>පද්ධතියට පිවිසීම</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="text-center mb-4">පද්ධතියට පිවිසීම</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>පරිශීලක නාමය</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                
                <div class="form-group">
                    <label>මුරපදය</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">පිවිසීම</button>
            </form>
        </div>
    </div>
</body>
</html>
