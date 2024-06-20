<?php
session_start();
include('functions.php'); // データベース接続関数とセッションチェック関数を含むファイルをインクルード

check_session_id(); // セッションIDの確認

$conn = connect_to_db();

$uploadDir = './uploads/'; // アップロード先のディレクトリ

// アップロードディレクトリが存在しない場合は作成
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploadFile = $uploadDir . basename($_FILES['file_upload']['name']);
$title = $_POST['title'] ?? ''; // タイトルがフォームから送信される場合
$description = $_POST['description'] ?? ''; // 説明がフォームから送信される場合
$user_id = $_SESSION['user_id']; // セッションからユーザーIDを取得

// ファイルをアップロードし、データベースに挿入
if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $uploadFile)) {
    $file_path = $uploadFile;

    // SQL文を準備して実行
    $stmt = $conn->prepare("INSERT INTO photo_table (user_id, title, description, file_path, created_at) VALUES (:user_id, :title, :description, :file_path, NOW())");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':file_path', $file_path, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo 'アップロード完了';
    } else {
        echo 'データベースへの挿入に失敗しました: ' . $stmt->errorInfo()[2];
    }
} else {
    echo 'アップロード失敗';
    echo '<br>エラー情報: ';
    print_r($_FILES);
}
?>


