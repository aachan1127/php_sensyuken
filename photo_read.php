<?php
session_start();
include("functions.php");
check_session_id();

// ーーーーーー　ユーザーidを取得　ーーーーーーーー
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];


$pdo = connect_to_db();

// プロフィール情報を取得
$sql_profile = "SELECT profile_image FROM profile_table WHERE user_id = :user_id";
$stmt_profile = $pdo->prepare($sql_profile);
$stmt_profile->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
  $stmt_profile->execute();
  $profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$sql = "SELECT * FROM photo_table ORDER BY created_at ASC";
// $sql = 'SELECT * FROM photo_table LEFT OUTER JOIN (SELECT photo_id, COUNT(id) AS cawaii_count FROM cawaii_table GROUP BY photo_id) AS cawaii_table ON photo_table.id = cawaii_photo.user_id';
// (SELECT todo_id, COUNT(id) AS like_count FROM like_table GROUP BY todo_id) 
// 👆は、集計結果のテーブル



$stmt = $pdo->prepare($sql);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$output = "";
foreach ($result as $record) {
  $output .=  "
  <tr>
    <td>{$record["created_at"]}</td>
    <td>{$record["title"]}</td>
    <td><a href='photo_edit.php?id={$record["id"]}'>edit</a></td>
    <td><a href='photo_delete.php?id={$record["id"]}'>delete</a></td>
  </tr>
";
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>dog写真（一覧画面）</title>
</head>

<body>
  <fieldset>
    <legend>dog写真（一覧画面）</legend>


    <?php if (!empty($profile['profile_image'])): ?>
        <img src="<?= htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" style="max-width: 70px; max-height: 70px; margin-right: 10px;">
    <?php endif; ?>        <p> ユーザー名：<?= $user_name ?></p>
     
    <a href="photo_input.php">写真投稿画面</a>
    <a href="dogSNS_logout.php">logout</a>
    <a href="dogSNS_prof.php">プロフィール登録</a>
    <a href="map.php">MAPからお友だち検索</a>
    <a href="dogSNS_mypage.php">マイページ</a>
    <a href="otomodachi_list.php">お友だちリスト</a>


    <table>
      <!-- <thead>
        <tr>
          <th>deadline</th>
          <th>todo</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?= $output ?>
      </tbody> -->


      <?php
        $uploadDir = './uploads/';
        $files = array_diff(scandir($uploadDir), array('.', '..'));

        foreach ($files as $file) {
            // Kaoの画像をスキップするための条件
            if (strpos($file, 'kao') === false) {
                $filePath = $uploadDir . $file;
                echo '<div>';
                echo '<img src="' . $filePath . '" alt="' . $file . '" style="max-width: 100px; max-height: 100px; margin: 10px;">';
                echo '</div>';
            }
        }
        ?>




    </table>
  </fieldset>
</body>

</html>