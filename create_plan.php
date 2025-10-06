<?php
require 'db.php';

// ログインしていなければリダイレクト
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ▼ マスタデータを取得
$ponds   = $pdo->prepare('SELECT id, number FROM ponds WHERE user_id=? ORDER BY number');
$ponds->execute([$_SESSION['user_id']]);
$ponds = $ponds->fetchAll();

$species = $pdo->query('SELECT id, name_en FROM species ORDER BY name_en')->fetchAll();
$regions = $pdo->query('SELECT id, name_en FROM regions ORDER BY name_en')->fetchAll();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = "INSERT INTO plans
          (user_id, pond_id, species_id, target_size_kg, target_harvest_date, sell_price_khrkg, region_id, created_at)
          VALUES (:user_id, :pond_id, :species_id, :target_size_kg, :target_harvest_date, :sell_price_khrkg, :region_id, NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':pond_id' => $_POST['pond_id'],
    ':species_id' => $_POST['species_id'],
    ':target_size_kg' => $_POST['target_size_kg'],
    ':target_harvest_date' => $_POST['target_harvest_date'] ?: null,
    ':sell_price_khrkg' => $_POST['sell_price_khrkg'],
    ':region_id' => $_POST['region_id'],
  ]);
  $message = '✅ 新しい計画を登録しました！';
}
?>

<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>新規計画登録 - FarmPlan</title>
  <style>
    body { font-family: sans-serif; background: #f9fafb; margin: 2em; }
    form {
      background: #fff; padding: 1.5em; border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 480px; margin: auto;
    }
    input, select { width: 100%; padding: 8px; margin: 6px 0; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: 10px 20px; background: #0A84FF; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0066cc; }
    .back { text-align: center; margin-top: 1em; }
  </style>
</head>
<body>
  <h2 style="text-align:center;">新しい計画を登録</h2>

  <?php if ($message): ?>
    <p style="color:green; text-align:center;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post">
    <label>池を選択：</label>
    <select name="pond_id" required>
      <option value="">選択してください</option>
      <?php foreach ($ponds as $p): ?>
        <option value="<?= $p['id'] ?>">Pond <?= htmlspecialchars($p['number']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>魚種：</label>
    <select name="species_id" required>
      <option value="">選択してください</option>
      <?php foreach ($species as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name_en']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>目標サイズ (kg/尾)：</label>
    <input type="number" step="0.01" name="target_size_kg" value="0.50" required>

    <label>収穫予定日：</label>
    <input type="date" name="target_harvest_date" value="2025-02-21">

    <label>販売価格 (KHR/kg)：</label>
    <input type="number" name="sell_price_khrkg" value="5000" required>

    <label>販売地域：</label>
    <select name="region_id" required>
      <option value="">選択してください</option>
      <?php foreach ($regions as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name_en']) ?></option>
      <?php endforeach; ?>
    </select>

    <button type="submit">登録する</button>
  </form>

  <div class="back">
    <a href="list_plans.php">← 計画一覧へ戻る</a>
  </div>
</body>
</html>
