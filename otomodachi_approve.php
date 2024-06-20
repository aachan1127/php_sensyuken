<?php
session_start();
include("functions.php");
check_session_id();

if (!isset($_POST['match_id']) || empty($_POST['match_id'])) {
    echo "paramError";
    exit();
}

$match_id = $_POST['match_id'];

$pdo = connect_to_db();

// ステータスを'承認済み'に更新
$sql = "UPDATE match_table SET status = '承認済み' WHERE id = :match_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':match_id', $match_id, PDO::PARAM_INT);

try {
    if ($stmt->execute()) {
        header("Location: otomodachi_list.php?status=approved");
        exit();
    } else {
        echo "データベースの更新に失敗しました: " . $stmt->errorInfo()[2];
    }
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}
?>
