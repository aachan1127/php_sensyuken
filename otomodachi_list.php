<?php
session_start();
include("functions.php");
check_session_id();

$pdo = connect_to_db();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// ログインしているユーザーの犬の名前を取得
$sql_dog_names = "SELECT dog_name FROM profile_table WHERE user_id = :user_id";
$stmt_dog_names = $pdo->prepare($sql_dog_names);
$stmt_dog_names->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt_dog_names->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$current_user_dog_names = $stmt_dog_names->fetchAll(PDO::FETCH_COLUMN);

// リクエスト申請が来ているユーザーを取得
$sql = "
SELECT m.id, u.username, p.dog_name, p.profile_image 
FROM match_table m
JOIN users_table u ON m.user_id_1 = u.id
JOIN profile_table p ON m.user_id_1 = p.user_id
WHERE m.user_id_2 = :user_id AND m.status = '承認待ち'
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 承認済みのユーザーを取得
$sql_approved = "
SELECT u.username, p.dog_name, p.profile_image, p.kao
FROM match_table m
JOIN users_table u ON (m.user_id_1 = u.id OR m.user_id_2 = u.id)
JOIN profile_table p ON (m.user_id_1 = p.user_id OR m.user_id_2 = p.user_id)
WHERE (m.user_id_1 = :user_id OR m.user_id_2 = :user_id) AND m.status = '承認済み'
";



$stmt_approved = $pdo->prepare($sql_approved);
$stmt_approved->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $stmt_approved->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$results_approved = $stmt_approved->fetchAll(PDO::FETCH_ASSOC);

// var_dump($results_approved);
// exit();

?>


<!DOCTYPE html>
<html>
<head>
    <title>リクエスト申請一覧</title>
    <!-- jQueryの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
    <h1>リクエスト申請一覧</h1>
    <a href="map.php">MAPからお友だち検索ページに戻る</a>
    <a href="photo_read.php">ホームに戻る</a>

    <h2>承認待ちリクエスト</h2>
    <!-- $resultsが空かどうかを確認 -->
    <?php if (empty($results)): ?>
        <p>リクエスト申請がありません。</p>

        <!-- $resultsにデータがある場合 -->
    <?php else: ?>
        <ul>
            <!-- $results内の各レコードをループする -->
            <?php foreach ($results as $result): ?>

                <li>
                    ユーザー名: <?= htmlspecialchars($result['username'], ENT_QUOTES, 'UTF-8') ?>, 
                    犬の名前: <?= htmlspecialchars($result['dog_name'], ENT_QUOTES, 'UTF-8') ?>
                    <?php if (!empty($result['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($result['profile_image'], ENT_QUOTES, 'UTF-8') ?>" alt="プロフィール画像" style="max-width: 70px; max-height: 70px; margin-right: 10px;">
                    <?php endif; ?>

    <form action="otomodachi_approve.php" method="POST">
    <button id="approve">お友だちになる</button>
    <input type="hidden" name="match_id" value="<?= $result['id']?>">
    </form>
                </li>

                <!-- ループ終わり -->
            <?php endforeach; ?>
        </ul>
        <!-- 条件分岐終わり -->
    <?php endif; ?>

    <h2>承認済みの友だち</h2>
    <?php if (empty($results_approved)): ?>
        <p>承認済みの友だちがいません。</p>
    <?php else: ?>
        <ul id="approvedFriends"></ul>
        <?php endif; ?>

    <!-- JSON形式で結果をJavaScriptに渡す -->
    <script>
        var pendingRequests = <?= json_encode($results) ?>;
        var approvedFriends = <?= json_encode($results_approved) ?>;
        var currentUser = '<?= $user_name ?>'; // ログインしているユーザーの名前を渡す
        var currentUserDogNames = <?= json_encode($current_user_dog_names) ?>; // ログインしているユーザーの犬の名前を渡す
    </script>
    <!-- jQueryでリストを生成 -->
    <script>
        $(document).ready(function () {
            // 承認済みの友だちを表示
            if (approvedFriends.length > 0) {
                var uniqueUsers = {};
                $.each(approvedFriends, function (index, result) {
                    if (result.username !== currentUser && !currentUserDogNames.includes(result.dog_name)) { // ログインしているユーザーの情報を表示しない
                        var uniqueKey = result.username + '-' + result.dog_name;
                        if (!uniqueUsers[uniqueKey]) {
                            var profileImageHtml = result.profile_image ? '<img src="' + result.profile_image + '" alt="プロフィール画像" style="max-width: 70px; max-height: 70px; margin-right: 10px;">' : '';
                            var kaoImageHtml = result.kao ? '<img src="' + result.kao + '" alt="飼い主の写真" style="max-width: 70px; max-height: 70px; margin-right: 10px;">' : '';
                            var listItem = '<li>' + profileImageHtml + kaoImageHtml + 'ユーザー名: ' + result.username + ', 犬の名前: ' + result.dog_name + '</li>';
                            $('#approvedFriends').append(listItem);
                            uniqueUsers[uniqueKey] = true;
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>