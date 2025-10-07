<?php
// -----------------------------------------------
// FarmPlan 共通データベース接続ファイル
// -----------------------------------------------

// セッション開始（ログイン情報を保持）
session_start();

// データベース設定
$dbn = 'mysql:dbname=FarmPlan;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';  // XAMPP既定ユーザー
$pwd  = '';      // パスワード（通常は空）

try {
  // PDOで接続
  $pdo = new PDO($dbn, $user, $pwd);
  // エラーモードを「例外」に設定
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // デフォルトのフェッチモードを連想配列に設定
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  exit('DB接続エラー: ' . $e->getMessage());
}
