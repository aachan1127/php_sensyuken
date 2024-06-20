<?php
session_start();
include('functions.php');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error_msg" => "no session"]);
    exit();
}

$user_id = $_SESSION["user_id"];

$dog_name = $_POST["dog_name"];
$dog_breeds = $_POST["dog_breeds"];
$dog_birth = $_POST["dog_birth"];
$user_address = $_POST["user_address"];
$interests = $_POST["interests"];
$comment = $_POST["comment"];

$pdo = connect_to_db();

// ファイルのアップロード処理
$upload_dir = './uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$profile_image = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['profile_image']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        echo "許可されていないファイルタイプです。";
        exit();
    } else {
        $profile_image = $uploadDir . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image);
    }
}


$kao = null;
if (isset($_FILES['kao']) && $_FILES['kao']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['kao']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        echo "許可されていないファイルタイプです。";
        exit();
    } else {
        $kao = $uploadDir . basename($_FILES['kao']['name']);
        move_uploaded_file($_FILES['kao']['tmp_name'], $kao);
    }
}

$sql = 'INSERT INTO profile_table(user_id, dog_name, dog_breeds, dog_birth, user_address, interests, comment, profile_image, kao) VALUES(:user_id, :dog_name, :dog_breeds, :dog_birth, :user_address, :interests, :comment, :profile_image, :kao)';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':dog_name', $dog_name, PDO::PARAM_STR);
$stmt->bindValue(':dog_breeds', $dog_breeds, PDO::PARAM_STR);
$stmt->bindValue(':dog_birth', $dog_birth, PDO::PARAM_STR);
$stmt->bindValue(':user_address', $user_address, PDO::PARAM_STR);
$stmt->bindValue(':interests', $interests, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
$stmt->bindValue(':kao', $kao, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header("Location:photo_read.php");
exit();
