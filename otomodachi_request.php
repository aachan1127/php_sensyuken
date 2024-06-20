<?php
session_start();
include("functions.php");
check_session_id();

$pdo = connect_to_db();


// クエリパラメータからユーザーIDを取得
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
// var_dump($_GET);
// exit();

// ユーザー情報を取得
$sql = "SELECT p.user_id, p.dog_name, p.comment, p.interests, p.dog_birth, u.username FROM profile_table p JOIN users_table u ON p.user_id = u.id WHERE p.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);


try {
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "ユーザーIDが指定されていません。";
    exit();
}

// $user = $stmt->fetch(PDO::FETCH_ASSOC);

// echo "<pre>";
// var_dump($user);
// echo "</pre>";
// exit();
// オッケー！！
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
     <!-- jqueryの読み込み -->
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<a href="map.php">MAPからお友だち検索ページに戻る</a>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <p style="color: green;">リクエストが完了しました</p>
<?php elseif (isset($_GET['status']) && $_GET['status'] == 'already_requested'): ?>
    <p style="color: red;">既にリクエストが送信されています</p>
<?php endif; ?>


    <h1><?= htmlspecialchars($user_info['username']) ?>のわんちゃん</h1>
    <p>犬の名前: <?= htmlspecialchars($user_info['dog_name']) ?></p>
    <p>犬の年齢: <?= htmlspecialchars($user_info['dog_birth']) ?></p>
    <p>友達になりたい犬: <?= htmlspecialchars($user_info['interests']) ?></p>
    <p>コメント: <?= htmlspecialchars($user_info['comment']) ?></p>
    
    <form action="otomodachi_request_act.php" method="POST">
    <button id="request">お友だちリクエスト</button>
    <input type="hidden" name="user_id_2" value="<?= $user_info['user_id'] ?>">
</form>

    <script>

$('#request').click(function(){
alert("お友だちリクエストを送りました")
});

    </script>



</body>
</html>
