<?php
session_start();
include("functions.php");
check_session_id();

// ーーーーーー　ユーザーidを取得　ーーーーーーーー
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

$pdo = connect_to_db();

// // クエリパラメータからユーザーIDを取得
// if (isset($_GET['user_id'])) {
//     $user_id = $_GET['user_id'];
// var_dump($_GET);
// exit();

 // ユーザー情報を取得
 $sql = "SELECT p.user_id, p.dog_name, p.comment, p.interests, p.dog_birth, p.dog_breeds, p.profile_image, p.kao, u.username FROM profile_table p JOIN users_table u ON p.user_id = u.id WHERE p.user_id = :user_id";
 $stmt = $pdo->prepare($sql);
 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);


 try {
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// ログインしているユーザーの写真を取得
$sql = "SELECT * FROM photo_table WHERE user_id = :user_id ORDER BY created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
<h1><?= htmlspecialchars($user_info['username']) ?>のプロフィール</h1>
    <?php if (!empty($user_info['profile_image'])): ?>
        <img src="<?= htmlspecialchars($user_info['profile_image'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" style="max-width: 100px; max-height: 100px;">
    <?php endif; ?>
    <?php if (!empty($user_info['kao'])): ?>
        <img src="<?= htmlspecialchars($user_info['kao'], ENT_QUOTES, 'UTF-8') ?>" alt="飼い主の写真" style="max-width: 100px; max-height: 100px;">
    <?php endif; ?>
    <p>犬の名前: <?= htmlspecialchars($user_info['dog_name']) ?></p>
    <p>犬種: <?= htmlspecialchars($user_info['dog_breeds']) ?></p>
    <p>犬の年齢: <?= htmlspecialchars($user_info['dog_birth']) ?></p>
    <p>友達になりたい犬: <?= htmlspecialchars($user_info['interests']) ?></p>
    <p>コメント: <?= htmlspecialchars($user_info['comment']) ?></p>

    <fieldset>
        <legend>dog写真（一覧画面）</legend>
        
        <p>ユーザー名：<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></p>
        <a href="photo_input.php">写真投稿画面</a>
        <a href="dogSNS_logout.php">logout</a>
        <a href="dogSNS_prof.php">プロフィール登録</a>
        <a href="map.php">MAPからお友だち検索</a>
        <a href="dogSNS_mypage.php">マイページ</a>
        <a href="otomodachi_list.php">お友だちリスト</a>
        <a href="photo_read.php">ホームに戻る</a>

        <table>
            <thead>
                <tr>
                    <th>投稿日</th>
                    <th>タイトル</th>
                    <th>説明</th>
                    <th>画像</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($photos as $photo): ?>
                    <tr>
                        <td><?= htmlspecialchars($photo['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($photo['description'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><img src="<?= htmlspecialchars($photo['file_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($photo['title'], ENT_QUOTES, 'UTF-8') ?>" style="max-width: 100px; max-height: 100px;"></td>
                        <td><a href="photo_edit.php?id=<?= $photo['id'] ?>">edit</a></td>
                        <td><a href="photo_delete.php?id=<?= $photo['id'] ?>">delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>

</body>
</html>
