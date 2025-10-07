<<?php
require 'db.php';
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$message = '';
$pond_id = $_GET['pond_id'] ?? null;
$new_plan_id = null;

// ▼ 魚種リストと地域リストを取得（ドロップダウン用）
$species = $pdo->query('SELECT id, name_en FROM species ORDER BY id ASC')->fetchAll();
$regions = $pdo->query('SELECT id, name_en FROM regions ORDER BY id ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ------------------------------------------------------------
  // 計画を登録（魚種・地域・収穫日・販売価格・目標サイズ）
  // ------------------------------------------------------------
  $sql = "INSERT INTO plans 
          (user_id, pond_id, species_id, target_size_kg, target_harvest_date, sell_price_khrkg, region_id, created_at)
          VALUES (:user_id, :pond_id, :species_id, :target_size_kg, :target_harvest_date, :sell_price_khrkg, :region_id, NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':pond_id' => $_POST['pond_id'],
    ':species_id' => $_POST['species_id'],
    ':target_size_kg' => $_POST['target_size_kg'],
    ':target_harvest_date' => $_POST['target_harvest_date'],
    ':sell_price_khrkg' => $_POST['sell_price_khrkg'],
    ':region_id' => $_POST['region_id']
  ]);
  $new_plan_id = $pdo->lastInsertId();

  // ------------------------------------------------------------
  // 稚魚情報を登録
  // ------------------------------------------------------------
  $sql2 = "INSERT INTO plan_fingerlings 
           (plan_id, unit_price_khr, stocking_number, created_at)
           VALUES (:plan_id, :unit_price_khr, :stocking_number, NOW())";
  $stmt2 = $pdo->prepare($sql2);
  $stmt2->execute([
    ':plan_id' => $new_plan_id,
    ':unit_price_khr' => $_POST['unit_price_khr'],
    ':stocking_number' => $_POST['stocking_number']
  ]);

  $message = "✅ 計画を登録しました！";
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>稚魚・計画入力 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
  <h2>稚魚・計画情報を登録</h2>

  <?php if ($message): ?>
    <div class="message success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="pond_id" value="<?= htmlspecialchars($pond_id) ?>">

    <!-- 魚種選択 -->
    <label>魚種：</label>
    <select name="species_id" required>
      <option value="">選択してください</option>
      <?php foreach ($species as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name_en']) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- 地域選択 -->
    <label>販売地域：</label>
    <select name="region_id" required>
      <option value="">選択してください</option>
      <?php foreach ($regions as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name_en']) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- 稚魚情報 -->
    <label>稚魚単価 (KHR/尾)：</label>
    <input type="number" name="unit_price_khr" value="150" required>

    <label>投入尾数：</label>
    <input type="number" name="stocking_number" value="30000" required>

    <!-- 計画情報 -->
    <label>目標サイズ (kg/尾)：</label>
    <input type="number" step="0.01" name="target_size_kg" value="0.5" required>

    <label>収穫予定日：</label>
    <input type="date" name="target_harvest_date" required>

    <label>販売価格 (KHR/kg)：</label>
    <input type="number" name="sell_price_khrkg" value="5000" required>

    <button type="submit" class="btn btn-primary">登録する</button>
  </form>

  <!-- ナビ -->
  <div class="nav-buttons">
    <a href="create_pond.php" class="btn btn-outline">← 戻る</a>
    <?php if ($new_plan_id): ?>
      <a href="plan_feed.php?plan_id=<?= $new_plan_id ?>" class="btn btn-success">次へ → Feed入力</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
