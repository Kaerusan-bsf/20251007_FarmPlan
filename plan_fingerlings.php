<?php
require 'db.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$message = '';
$pond_id = $_GET['pond_id'] ?? null;
$new_plan_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = "INSERT INTO plans (user_id, pond_id, species_id, target_size_kg, target_harvest_date, sell_price_khrkg, region_id, created_at)
          VALUES (:user_id, :pond_id, 1, 0.5, '2025-02-21', 5000, 1, NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':user_id' => $_SESSION['user_id'], ':pond_id' => $_POST['pond_id']]);
  $new_plan_id = $pdo->lastInsertId();

  $sql2 = "INSERT INTO plan_fingerlings (plan_id, unit_price_khr, stocking_number, created_at)
           VALUES (:plan_id, :unit_price_khr, :stocking_number, NOW())";
  $stmt2 = $pdo->prepare($sql2);
  $stmt2->execute([
    ':plan_id' => $new_plan_id,
    ':unit_price_khr' => $_POST['unit_price_khr'],
    ':stocking_number' => $_POST['stocking_number']
  ]);

  $message = "✅ 稚魚情報を登録しました！";
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>稚魚入力 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h2>稚魚情報を登録</h2>
  <?php if ($message): ?><div class="message success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="pond_id" value="<?= htmlspecialchars($pond_id) ?>">
    <label>稚魚単価 (KHR/尾)：</label><input type="number" name="unit_price_khr" value="150" required>
    <label>投入尾数：</label><input type="number" name="stocking_number" value="30000" required>
    <button type="submit" class="btn btn-primary">登録する</button>
  </form>
  <div class="nav-buttons">
    <a href="create_pond.php" class="btn btn-outline">← 戻る</a>
    <?php if ($new_plan_id): ?>
      <a href="plan_feed.php?plan_id=<?= $new_plan_id ?>" class="btn btn-success">次へ → Feed入力</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
