<?php
require 'db.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$sql = "SELECT p.id, po.number AS pond_number, s.name_en AS species, r.name_en AS region,
               p.target_size_kg, p.sell_price_khrkg, p.target_harvest_date, p.created_at
        FROM plans p
        JOIN ponds po ON po.id = p.pond_id
        JOIN species s ON s.id = p.species_id
        JOIN regions r ON r.id = p.region_id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$plans = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>計画一覧 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <h2>FarmPlan 計画一覧</h2>
  <p>👤 <?= htmlspecialchars($_SESSION['username']) ?> さん | <a href="logout.php">ログアウト</a></p>
  <a href="create_pond.php" class="btn btn-primary">＋ 新しい計画を登録（池入力へ）</a>
  <hr>
  <?php if (count($plans) === 0): ?>
    <p>まだ登録された計画がありません。</p>
  <?php else: ?>
    <table>
      <tr><th>ID</th><th>池番号</th><th>魚種</th><th>地域</th><th>目標サイズ</th><th>販売価格</th><th>収穫予定</th></tr>
      <?php foreach ($plans as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['pond_number']) ?></td>
        <td><?= htmlspecialchars($p['species']) ?></td>
        <td><?= htmlspecialchars($p['region']) ?></td>
        <td><?= $p['target_size_kg'] ?>kg</td>
        <td><?= number_format($p['sell_price_khrkg']) ?>KHR/kg</td>
        <td><?= htmlspecialchars($p['target_harvest_date']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
