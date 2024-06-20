<?php
session_start();
include('functions.php');

$username = $_POST['username'];
$password = $_POST['password'];

$pdo = connect_to_db();

$sql = 'SELECT * FROM users_table WHERE username=:username AND password=:password AND deleted_at IS NULL';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  echo "<p>ログイン情報に誤りがあります</p>";
  echo "<a href=dogSNS_login.php>ログイン</a>";
  exit();
} else {
  $_SESSION = array();

   // ーーーーー　🔽 ここを追記　ーーーーーーー
   $_SESSION['user_id'] = $user['id'];
// ーーーーーーーーーーーーーーーーーーーーーーーー
  $_SESSION['session_id'] = session_id();
  $_SESSION['is_admin'] = $user['is_admin'];
  $_SESSION['username'] = $user['username'];
  header("Location:photo_read.php");
  exit();
}
