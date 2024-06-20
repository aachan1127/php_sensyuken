<?php
// var_dump($_POST);
// exit();

session_start();
include("functions.php");
check_session_id();



if (!isset($_POST['user_id_2']) || empty($_POST['user_id_2'])) {
    echo "paramError";
    exit();
}

$user_id_1 = $_SESSION['user_id'];
$user_id_2 = $_POST['user_id_2'];

// var_dump($_POST);
// exit();

$pdo = connect_to_db();

// 既存のリクエストがあるか確認
$sql = "SELECT * FROM match_table WHERE user_id_1 = :user_id_1 AND user_id_2 = :user_id_2 AND status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id_1', $user_id_1, PDO::PARAM_INT);
$stmt->bindValue(':user_id_2', $user_id_2, PDO::PARAM_INT);
$stmt->execute();
$existing_request = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_request) {
    // 既にリクエストが存在する場合
    header("Location: otomodachi_request.php?user_id=$user_id_2&status=already_requested");
    exit();
}

// 新しいリクエストを挿入
$sql = "INSERT INTO match_table (user_id_1, user_id_2, status) VALUES (:user_id_1, :user_id_2, '承認待ち')";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id_1', $user_id_1, PDO::PARAM_INT);
$stmt->bindValue(':user_id_2', $user_id_2, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: otomodachi_request.php?user_id=$user_id_2&status=success");
    exit();
} else {
    echo "データベースへの挿入に失敗しました: " . $stmt->errorInfo()[2];
}
?>
