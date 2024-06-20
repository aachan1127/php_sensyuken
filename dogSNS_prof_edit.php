<?php
session_start();
include("functions.php");
check_session_id();


// DB接続
$pdo = connect_to_db();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];


// プロフィール情報を取得
$sql = "SELECT * FROM profile_table WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);


try {
  $stmt->execute();
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

if (!$profile) {
  echo "プロフィール情報が見つかりません。";
  exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール編集</title>
</head>
<body>
    <form action="dogSNS_prof_update.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>プロフィール編集</legend>
            <div>
                わんちゃんの名前: <input type="text" name="dog_name" value="<?= htmlspecialchars($profile['dog_name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                犬種: <input type="text" name="dog_breeds" value="<?= htmlspecialchars($profile['dog_breeds'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                年齢: <input type="text" name="dog_birth" value="<?= htmlspecialchars($profile['dog_birth'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                住所: <input type="text" name="user_address" value="<?= htmlspecialchars($profile['user_address'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                仲良くなりたい犬: <input type="text" name="interests" value="<?= htmlspecialchars($profile['interests'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                コメント: <textarea name="comment"><?= htmlspecialchars($profile['comment'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                犬の写真: <input type="file" name="profile_image">
                <?php if (!empty($profile['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" style="max-width: 100px; max-height: 100px;">
                <?php endif; ?>
                </div>
                <div>
                飼い主の写真: <input type="file" name="kao">
                <?php if (!empty($profile['kao'])): ?>
                    <img src="<?= htmlspecialchars($profile['kao'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" style="max-width: 100px; max-height: 100px;">
                <?php endif; ?>
                </div>
            <div>
                <button type="submit">更新</button>
            </div>
        </fieldset>
    </form>
</body>
</html>
