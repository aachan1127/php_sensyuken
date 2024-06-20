<?php
session_start();
include("functions.php");
check_session_id();

if (
  !isset($_POST['todo']) || $_POST['todo'] === '' ||
  !isset($_POST['deadline']) || $_POST['deadline'] === ''
) {
  exit('paramError');
}

$todo = $_POST['todo'];
$deadline = $_POST['deadline'];

$pdo = connect_to_db();

$sql = 'INSERT INTO photo_table(id, todo, deadline, created_at, updated_at) VALUES(NULL, :todo, :deadline, now(), now())';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':todo', $todo, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:photo_input.php");
exit();
