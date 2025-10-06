<?php
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare('SELECT * FROM users WHERE username=? LIMIT 1');
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && $user['password'] === $password) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: list_plans.php');
    exit;
  } else {
    $error = 'ユーザー名またはパスワードが間違っています。';
  }
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>FarmPlan ログイン</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h2>FarmPlan ログイン</h2>
  <?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post">
    <label>ユーザー名：</label>
    <input type="text" name="username" required>
    <label>パスワード：</label>
    <input type="password" name="password" required>
    <button type="submit" class="btn btn-primary">ログイン</button>
  </form>
</div>
</body>
</html>
