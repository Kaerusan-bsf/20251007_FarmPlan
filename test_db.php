<?php
require 'db.php';
echo "✅ DB接続OK！<br>";

$stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
$row = $stmt->fetch();
echo "ユーザー数：" . $row['cnt'];
