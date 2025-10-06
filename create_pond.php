<?php
require 'db.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$message = '';
$new_pond_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = "INSERT INTO ponds (user_id, number, location, length_m, width_m, depth_m, created_at)
          VALUES (:user_id, :number, :location, :length_m, :width_m, :depth_m, NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':number' => $_POST['number'],
    ':location' => $_POST['location'],
    ':length_m' => $_POST['length_m'],
    ':width_m' => $_POST['width_m'],
    ':depth_m' => $_POST['depth_m']
  ]);
  $new_pond_id = $pdo->lastInsertId();
  $message = "✅ 池情報を登録しました！";
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>池情報入力 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h2>池情報を登録</h2>
  <?php if ($message): ?><div class="message success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <form method="post">
    <label>池番号：</label><input type="number" name="number" required>
    <label>所在地：</label><input type="text" name="location" required>
    <label>縦 (m)：</label><input type="number" step="0.1" name="length_m" required>
    <label>横 (m)：</label><input type="number" step="0.1" name="width_m" required>
    <label>平均水深 (m)：</label><input type="number" step="0.1" name="depth_m" required>
    <button type="submit" class="btn btn-primary">登録する</button>
  </form>
  <div class="nav-buttons">
    <a href="list_plans.php" class="btn btn-outline">← 一覧へ</a>
    <?php if ($new_pond_id): ?>
      <a href="plan_fingerlings.php?pond_id=<?= $new_pond_id ?>" class="btn btn-success">次へ → 稚魚入力</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
