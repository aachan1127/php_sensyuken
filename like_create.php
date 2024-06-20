<?php
// var_dump($_GET);
// exit();

include('functions.php');

$user_id = $_GET['user_id'];
$todo_id = $_GET['todo_id'];

$pdo = connect_to_db();

// ーーーーーーー　該当するデータが存在するかどうか確認　ーーーーーーーーー
$sql = 'SELECT COUNT(*) FROM cawaii_table WHERE user_id=:user_id AND todo_id=:todo_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// 件数を取得
$like_count = $stmt->fetchColumn();

// まずはデータ確認
// var_dump($like_count);
// exit();
// 　オッケー！！


if ($like_count !== 0) {
  // いいねされている状態
  $sql = 'DELETE FROM cawaii_table WHERE user_id=:user_id AND todo_id=:todo_id';
} else {
  // いいねされていない状態
  $sql = 'INSERT INTO cawaii_table (id, user_id, todo_id, created_at) VALUES (NULL, :user_id, :todo_id, now())';
}

// 以下は前項と変更なし
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:photo_read.php");
exit();


// ーーーーーーーー　いいねされていない時は👍ができるようにしたいので、この文を 条件分岐 のところに移動！　ーーーーーーーーーーー
// $sql = 'INSERT INTO like_table (id, user_id, todo_id, created_at) VALUES (NULL, :user_id, :todo_id, now())';

// $stmt = $pdo->prepare($sql);
// $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
// $stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_STR);

// try {
//   $status = $stmt->execute();
// } catch (PDOException $e) {
//   echo json_encode(["sql error" => "{$e->getMessage()}"]);
//   exit();
// }

// header("Location:todo_read.php");
// exit();