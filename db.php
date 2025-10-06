<?php
session_start(); // ログイン状態保持

$dbn = 'mysql:dbname=FarmPlan;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd  = '';

try {
  $pdo = new PDO($dbn, $user, $pwd);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit('DB接続エラー: ' . $e->getMessage());
}

