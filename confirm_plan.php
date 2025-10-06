<?php
require 'db.php';
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$result = null;
$message = '';
$plan_id = $_GET['plan_id'] ?? null;

// ------------------------------------------------------------
// データ取得＆計算処理
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $plan_id) {
  $plan_id = $_POST['plan_id'] ?? $plan_id;

  $plan = $pdo->prepare("SELECT * FROM plans WHERE id=?");
  $plan->execute([$plan_id]);
  $plan = $plan->fetch();

  $fry = $pdo->prepare("SELECT * FROM plan_fingerlings WHERE plan_id=? LIMIT 1");
  $fry->execute([$plan_id]);
  $fry = $fry->fetch();

  $mix = $pdo->prepare("SELECT * FROM plan_feed_mix WHERE plan_id=? LIMIT 1");
  $mix->execute([$plan_id]);
  $mix = $mix->fetch();

  if (!$plan || !$fry || !$mix) {
    $message = "⚠️ この計画に必要なデータ（稚魚・飼料）が揃っていません。";
  } else {
    // === 計算 ===
    $FX = 4000; // 固定レート
    $stock = $fry['stocking_number'];
    $targetW = $plan['target_size_kg'];
    $harvest_kg = $stock * $targetW;

    $fcr = max(1.2, round(3.5 - 0.08 * $mix['hf_blend_cp_pct'], 2));
    $feed_req_kg = $harvest_kg * $fcr;

    $blend_price = $mix['hf_blend_price_khrkg'] * ($mix['hf_ratio_pct'] / 100)
                 + $mix['cf_price_khrkg'] * (1 - $mix['hf_ratio_pct'] / 100);

    $feed_cost_usd = ($blend_price * $feed_req_kg) / $FX;
    $fry_cost_usd = ($fry['unit_price_khr'] * $stock) / $FX;
    $revenue_usd = ($plan['sell_price_khrkg'] * $harvest_kg) / $FX;
    $profit_usd = $revenue_usd - ($feed_cost_usd + $fry_cost_usd);
    $margin_pct = $revenue_usd > 0 ? round(($profit_usd / $revenue_usd) * 100, 1) : 0;

    // スナップショット保存
    $pdo->prepare("INSERT INTO calc_snapshots 
      (plan_id, harvest_weight_kg, feed_required_kg, feed_cost_usd, fry_cost_usd, revenue_usd, profit_usd, margin_pct, created_at)
      VALUES (?,?,?,?,?,?,?,?,NOW())")
      ->execute([$plan_id, $harvest_kg, $feed_req_kg, $feed_cost_usd, $fry_cost_usd, $revenue_usd, $profit_usd, $margin_pct]);

    $result = compact('harvest_kg', 'feed_req_kg', 'feed_cost_usd', 'fry_cost_usd', 'revenue_usd', 'profit_usd', 'margin_pct');
  }
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>確認画面 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
<style>
  .result-table { width:100%; border-collapse:collapse; margin-top:10px; background:#fff; }
  .result-table th, .result-table td {
    border:1px solid #ddd; padding:10px; text-align:left;
  }
  .result-table th { background:#e0f0ff; width:50%; }
  .summary-box {
    background:#fff; padding:1em; border-radius:10px; margin-top:1em;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
  }
  .profit { color:#2e7d32; font-weight:bold; font-size:1.2em; }
  .loss { color:#c62828; font-weight:bold; font-size:1.2em; }
</style>
</head>
<body>

<div class="container">
  <h2>結果確認</h2>

  <?php if ($message): ?>
    <div class="message error"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- ▼ 計画選択フォーム -->
  <form method="post">
    <label>対象の計画：</label>
    <input type="number" name="plan_id" placeholder="例：1" value="<?= htmlspecialchars($plan_id ?? '') ?>" required>
    <button type="submit" class="btn btn-primary">確認する</button>
  </form>

  <!-- ▼ 結果表示 -->
  <?php if ($result): ?>
  <div class="summary-box">
    <table class="result-table">
      <tr><th>収穫重量</th><td><?= number_format($result['harvest_kg']) ?> kg</td></tr>
      <tr><th>必要飼料量</th><td><?= number_format($result['feed_req_kg']) ?> kg</td></tr>
      <tr><th>飼料コスト</th><td>$<?= number_format($result['feed_cost_usd'], 2) ?></td></tr>
      <tr><th>稚魚コスト</th><td>$<?= number_format($result['fry_cost_usd'], 2) ?></td></tr>
      <tr><th>売上</th><td>$<?= number_format($result['revenue_usd'], 2) ?></td></tr>
      <tr><th>利益</th>
          <td class="<?= $result['profit_usd'] >= 0 ? 'profit' : 'loss' ?>">
            $<?= number_format($result['profit_usd'], 2) ?>
          </td>
      </tr>
      <tr><th>利益率</th><td><?= $result['margin_pct'] ?>%</td></tr>
    </table>
  </div>

  <div class="message success" style="margin-top:10px;">
    ✅ この計算結果はデータベースに保存されました（calc_snapshots）。
  </div>
  <?php endif; ?>

  <!-- ▼ ナビ -->
  <div class="nav-buttons">
    <a href="plan_feed.php?plan_id=<?= htmlspecialchars($plan_id) ?>" class="btn btn-outline">← 戻る</a>
    <a href="list_plans.php" class="btn btn-success">完了 → 計画一覧へ</a>
  </div>
</div>

</body>
</html>
