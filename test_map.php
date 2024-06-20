<?php
session_start();
include("functions.php");
check_session_id();

// ーーーーーー　ユーザーidを取得　ーーーーーーーー
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// var_dump();
// exit();

$pdo = connect_to_db();

// ーーーーーーー　ログインユーザーの住所を取得　ーーーーーーー
$sql = "SELECT user_address FROM profile_table WHERE user_id = :user_id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);



// ーーーーーーー　すべてのユーザーの住所を取得　ーーーーーーーー
// $sql = "SELECT user_id, user_address, username FROM profile_table JOIN users_table ON id = user_id WHERE user_id != :user_id";
$sql = "SELECT p.user_id, p.user_address, u.username FROM profile_table p JOIN users_table u ON p.user_id = u.id WHERE p.user_id != :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);


try {
    $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}


$all_user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);


//   echo json_encode($user_addresses);  // JSON形式で住所データを出力
//   exit();

// ######## ここJSON に値を渡したい時に使う #############
$json= json_encode($user_addresses,JSON_UNESCAPED_UNICODE);
$json_all_user_addresses = json_encode($all_user_addresses, JSON_UNESCAPED_UNICODE);
// ##################################################

?>

<!DOCTYPE html>
<html>
<head>
    <title>Nearby Users</title>
    <!-- jQueryの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Google Maps APIの読み込み。YOUR_GOOGLE_MAPS_API_KEYをあなたのAPIキーに置き換える -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAy5WewZMSULZCDBzc8mZ8TFYZ1TQxseL4&&libraries=geometry"></script>
</head>
<body>
    <h1>Nearby Users</h1>
    <p> ログインユーザー名：<?= $user_name ?></p>
    <div id="map" style="height: 500px; width: 100%;"></div>

    <div id="otomodachi_list"></div>

    <script>
        $(document).ready(function () {
            // 住所を緯度経度に変換する関数
            function geocodeAddress(geocoder, address) {
                return new Promise((resolve, reject) => {
                    geocoder.geocode({ 'address': address }, function (results, status) {
                        if (status === 'OK') {
                            var location = results[0].geometry.location;
                            resolve({ lat: location.lat(), lng: location.lng() });
                        } else {
                            reject('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                });
            }

            // 現在の位置から半径指定のユーザーを取得する関数
            function getNearbyUsers(users, currentLocation, radius) {
                var nearbyUsers = [];  // 近くのユーザーを格納する配列
                users.forEach(function (user) {  // 各ユーザーをループ
                    var userLocation = new google.maps.LatLng(user.lat, user.lng);  // ユーザーの緯度経度をLatLngオブジェクトに変換
                    var distance = google.maps.geometry.spherical.computeDistanceBetween(currentLocation, userLocation);  // 距離を計算
                    console.log(distance);
                    
                    if (distance <= radius) {  // 距離が半径以内ならnearbyUsersに追加
                        nearbyUsers.push(user);
                    }
                });
                return nearbyUsers;  // 近くのユーザーを返す
            }

            // 地図を初期化する関数
            function initMap() {
                var map = new google.maps.Map($('#map')[0], {
                    zoom: 15,
                    center: { lat: 33.5902, lng: 130.4017 }
                });
                var geocoder = new google.maps.Geocoder();

                // PHPから受け取ったJSONをJavaScriptのオブジェクトに変換
                var userAddress = JSON.parse('<?= json_encode($user_addresses[0], JSON_UNESCAPED_UNICODE); ?>').user_address;
                var allUserData = JSON.parse('<?= json_encode($all_user_addresses, JSON_UNESCAPED_UNICODE); ?>');
                var users = [];

                console.log(allUserData);
                // すべてのユーザーの住所を緯度経度に変換し、users配列に追加するPromiseを作成
                // ここの　map は配列の値にひとつずつ　計算を行って、配列に入れて戻すやつ！！！！！
                var userPromises = allUserData.map(function (user) {
                    return geocodeAddress(geocoder, user.user_address)
                        .then(function (location) {
                            users.push({ name: user.username, lat: location.lat, lng: location.lng });
                            
                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                });

                // メインのユーザーの住所を基に地図を設定し、全てのユーザーの住所変換が完了した後にgetNearbyUsersを呼び出す
                geocodeAddress(geocoder, userAddress)
                    .then(function (currentLocation) {
                        var currentLatLng = new google.maps.LatLng(currentLocation.lat, currentLocation.lng);
                        map.setCenter(currentLatLng);

                        new google.maps.Marker({
                            position: currentLatLng,
                            map: map,
                            title: '<?= $user_name ?>'
                        });


                // ーーーーーー　距離の範囲を色付けして表示する円を作成　ーーーーーーーー
                        var circle = new google.maps.Circle({
                            map: map,
                            radius: 1000,    // 指定した距離（メートル）
                            fillColor: '#AA0000',
                            fillOpacity: 0.35,
                            strokeColor: '#AA0000',
                            strokeOpacity: 0.8,
                            strokeWeight: 2
                        });
                        circle.setCenter(currentLatLng);



                        // すべての住所変換が完了するのを待つ
                        Promise.all(userPromises).then(function () {
                            var nearbyUsers = getNearbyUsers(users, currentLatLng, 1000);// 1000メートルの半径を指定
                            nearbyUsers.forEach(function (user) {
                                console.log(user.name);

                                // append　は名前をつけて保存
                                $('#otomodachi_list').append(`<p>${user.name}</p>`);

                                new google.maps.Marker({
                                    position: { lat: user.lat, lng: user.lng },
                                    map: map,
                                    title: user.name

                                });



                            });
                        });
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            }

            initMap();
        });
    </script>
</body>
</html>
