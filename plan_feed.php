<?php
require 'db.php';
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$message = '';
$plan_id = $_GET['plan_id'] ?? null;

// ------------------------------------------------------------
// 入力処理（原料登録 or HF/CF配合登録）
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['form_type'] === 'recipe') {
    // 原料登録
    $sql = "INSERT INTO plan_feed_recipe_items
            (plan_id, ingredient, ratio_pct, unit_price_khr, cp_pct, created_at)
            VALUES (:plan_id, :ingredient, :ratio_pct, :unit_price_khr, :cp_pct, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':plan_id' => $_POST['plan_id'],
      ':ingredient' => $_POST['ingredient'],
      ':ratio_pct' => $_POST['ratio_pct'],
      ':unit_price_khr' => $_POST['unit_price_khr'],
      ':cp_pct' => $_POST['cp_pct'],
    ]);
    $message = "✅ 原料を追加しました！";

  } elseif ($_POST['form_type'] === 'mix') {
    // HF/CF配合登録
    $sql = "INSERT INTO plan_feed_mix
            (plan_id, hf_ratio_pct, hf_blend_price_khrkg, hf_blend_cp_pct,
             cf_price_khrkg, cf_cp_pct, created_at)
            VALUES (:plan_id, :hf_ratio_pct, :hf_blend_price_khrkg, :hf_blend_cp_pct,
                    :cf_price_khrkg, :cf_cp_pct, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':plan_id' => $_POST['plan_id'],
      ':hf_ratio_pct' => $_POST['hf_ratio_pct'],
      ':hf_blend_price_khrkg' => $_POST['hf_blend_price_khrkg'],
      ':hf_blend_cp_pct' => $_POST['hf_blend_cp_pct'],
      ':cf_price_khrkg' => $_POST['cf_price_khrkg'],
      ':cf_cp_pct' => $_POST['cf_cp_pct']
    ]);
    $message = "✅ HF/CF配合を登録しました！";
  }
}

// ------------------------------------------------------------
// 登録済み原料の取得＋HFブレンド自動計算
// ------------------------------------------------------------
$recipe_rows = [];
$blend_price = 0;
$blend_cp = 0;

if ($plan_id) {
  $stmt = $pdo->prepare("SELECT ratio_pct, unit_price_khr, cp_pct, ingredient
                         FROM plan_feed_recipe_items
                         WHERE plan_id=? ORDER BY id ASC");
  $stmt->execute([$plan_id]);
  $recipe_rows = $stmt->fetchAll();

  // 自動HFブレンド計算（加重平均）
  $total_ratio = 0;
  foreach ($recipe_rows as $r) { $total_ratio += $r['ratio_pct']; }
  if ($total_ratio > 0) {
    foreach ($recipe_rows as $r) {
      $w = $r['ratio_pct'] / $total_ratio;
      $blend_price += $w * $r['unit_price_khr'];
      $blend_cp    += $w * $r['cp_pct'];
    }
    $blend_price = round($blend_price);
    $blend_cp = round($blend_cp, 1);
  }
}

// ------------------------------------------------------------
// HF/CF登録後の最終ブレンド結果を表示
// ------------------------------------------------------------
$hf_ratio = $hf_price = $hf_cp = $cf_price = $cf_cp = 0;
$total_blend_price = $total_blend_cp = null;

if ($plan_id) {
  $stmt = $pdo->prepare("SELECT * FROM plan_feed_mix WHERE plan_id=? ORDER BY id DESC LIMIT 1");
  $stmt->execute([$plan_id]);
  $mix = $stmt->fetch();

  if ($mix) {
    $hf_ratio = $mix['hf_ratio_pct'];
    $hf_price = $mix['hf_blend_price_khrkg'];
    $hf_cp    = $mix['hf_blend_cp_pct'];
    $cf_price = $mix['cf_price_khrkg'];
    $cf_cp    = $mix['cf_cp_pct'];

    // HF/CF混合後の最終ブレンド価格・CPを計算
    $total_blend_price = round(($hf_ratio / 100) * $hf_price + ((100 - $hf_ratio) / 100) * $cf_price);
    $total_blend_cp    = round(($hf_ratio / 100) * $hf_cp + ((100 - $hf_ratio) / 100) * $cf_cp, 1);
  }
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Feed入力 - FarmPlan</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
  <h2>Feed情報を登録</h2>

  <?php if ($message): ?>
    <div class="message success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- ---------------------------------------------------------- -->
  <!-- 🧱 原料登録フォーム -->
  <!-- ---------------------------------------------------------- -->
  <form method="post">
    <input type="hidden" name="form_type" value="recipe">
    <input type="hidden" name="plan_id" value="<?= htmlspecialchars($plan_id) ?>">

    <label>材料名：</label>
    <input type="text" name="ingredient" placeholder="例: Rice bran" required>

    <label>比率(%)：</label>
    <input type="number" step="0.1" name="ratio_pct" required>

    <label>単価(KHR/kg)：</label>
    <input type="number" name="unit_price_khr" required>

    <label>CP(%)：</label>
    <input type="number" step="0.1" name="cp_pct" required>

    <button type="submit" class="btn btn-primary">＋ 原料を追加</button>
  </form>

  <!-- ---------------------------------------------------------- -->
  <!-- 📋 登録済み原料一覧 -->
  <!-- ---------------------------------------------------------- -->
  <?php if (count($recipe_rows) > 0): ?>
    <table>
      <tr><th>材料名</th><th>比率%</th><th>単価(KHR/kg)</th><th>CP%</th></tr>
      <?php foreach($recipe_rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['ingredient']) ?></td>
          <td><?= htmlspecialchars($r['ratio_pct']) ?></td>
          <td><?= htmlspecialchars($r['unit_price_khr']) ?></td>
          <td><?= htmlspecialchars($r['cp_pct']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <!-- 自動計算結果表示 -->
    <div class="message success" style="margin-top:10px;">
      🧮 HFブレンド結果：<br>
      💰 価格 = <?= number_format($blend_price) ?> KHR/kg<br>
      🧬 CP = <?= $blend_cp ?> %
    </div>
  <?php endif; ?>

  <!-- ---------------------------------------------------------- -->
  <!-- ⚙️ HF/CF配合フォーム -->
  <!-- ---------------------------------------------------------- -->
  <form method="post">
    <input type="hidden" name="form_type" value="mix">
    <input type="hidden" name="plan_id" value="<?= htmlspecialchars($plan_id) ?>">

    <label>HF比率(%)：</label>
    <input type="number" name="hf_ratio_pct" value="<?= htmlspecialchars($hf_ratio ?: 50) ?>" required>

    <label>HFブレンド価格(KHR/kg)：</label>
    <input type="number" name="hf_blend_price_khrkg" value="<?= htmlspecialchars($blend_price) ?>" readonly style="background:#eee;">

    <label>HFブレンドCP(%)：</label>
    <input type="number" step="0.1" name="hf_blend_cp_pct" value="<?= htmlspecialchars($blend_cp) ?>" readonly style="background:#eee;">

    <label>CF価格(KHR/kg)：</label>
    <input type="number" name="cf_price_khrkg" value="<?= htmlspecialchars($cf_price ?: 3200) ?>" required>

    <label>CF CP(%)：</label>
    <input type="number" step="0.1" name="cf_cp_pct" value="<?= htmlspecialchars($cf_cp ?: 15) ?>" required>

    <button type="submit" class="btn btn-primary">配合を登録</button>
  </form>

  <!-- ---------------------------------------------------------- -->
  <!-- 🧮 最終ブレンド結果 -->
  <!-- ---------------------------------------------------------- -->
  <?php if ($total_blend_price !== null): ?>
    <div class="message success" style="margin-top:10px;">
      🧮 最終ブレンド結果：<br>
      💰 価格 = <?= number_format($total_blend_price) ?> KHR/kg<br>
      🧬 CP = <?= $total_blend_cp ?> %
    </div>
  <?php endif; ?>

  <!-- ---------------------------------------------------------- -->
  <!-- 🔘 ナビゲーション -->
  <!-- ---------------------------------------------------------- -->
  <div class="nav-buttons">
    <a href="plan_fingerlings.php" class="btn btn-outline">← 戻る</a>
    <a href="confirm_plan.php?plan_id=<?= htmlspecialchars($plan_id) ?>" class="btn btn-success">次へ → 確認画面へ</a>
  </div>
</div>

</body>
</html>
