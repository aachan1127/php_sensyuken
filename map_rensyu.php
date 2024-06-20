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
$json = json_encode($user_addresses,JSON_UNESCAPED_UNICODE);
$json_all_user_addresses = json_encode($all_user_addresses, JSON_UNESCAPED_UNICODE);
// ##################################################

?>

<!DOCTYPE html>
<html>

<head>
    <title>Nearby Users</title>
    <!-- jqueryの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- APIキーの後ろ、 geometryにすることで地理空間の計算ができる！
            geometry.spherical.computeDistanceBetweenメソッドなどが含まれる-->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo$API_KEY?>&&libraries=geometry"></script>

</head>

<body>
    <h1>あなたの近くユーザー</h1>
    <p> ログインユーザー名：<?= $user_name ?></p>
    <div id="map" style="height: 500px; width: 100%;"></div>

    <script>
// JSONの受け取り
$a = '<?=$json?>';
// console.log($a);

// JSONをオブジェクトに変換
const obj = JSON.parse($a);
console.log(obj);


// JSONの受け取り
$b = '<?=$json_all_user_addresses?>';
// console.log($a);

// JSONをオブジェクトに変換
const obj2 = JSON.parse($b);
console.log(obj2);

// var users = [];


//                 // 各ユーザーの住所を緯度経度に変換し、users配列に追加
//                 obj2.forEach(function (user) {
//                     geocodeAddress(geocoder, user.user_address, function (lat, lng) {
//                         if (lat && lng) {
//                             users.push({ name: user.username, lat: lat, lng: lng });
//                         }
//                     });
//                 });

//                 console.log(users);



        $(document).ready(function () {
            // 住所を緯度経度に変換する関数
            function geocodeAddress(geocoder, address) {
                return new Promise((resolve, reject) => {
                // geocoder.geocodeメソッドを使用して住所を緯度経度に変換
                geocoder.geocode({ 'address': address }, function (results, status) {
                    // ステータスがOKなら緯度経度をコールバック関数に渡す
                    if (status === 'OK') {
                        var location = results[0].geometry.location;
                        // callback(location.lat(), location.lng());
                    } else {

                        reject('Geocode was not successful for the following reason: ' + status);
                        // エラーハンドリング
                        // console.error('Geocode was not successful for the following reason: ' + status);
                        // callback(null, null);
                    }
                });
            });
            }

            // 現在の位置から半径500m以内のユーザーを取得する関数
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
                // 地図を作成し、指定された位置とズームレベルで初期化
                var map = new google.maps.Map($('#map')[0], {
                    zoom: 15,
                    center: { lat: 33.5902, lng: 130.4017 }  // 福岡県福岡市を初期位置に設定
                });
                var geocoder = new google.maps.Geocoder();  // Geocoderインスタンスを作成

                // デモ用の登録者データ
                var users = [];


// 各ユーザーの住所を緯度経度に変換し、users配列に追加
// obj2.forEach(function (user) {
//     geocodeAddress(geocoder, user.user_address, function (lat, lng) {
//         if (lat && lng) {
//             users.push({ name: user.username, lat: lat, lng: lng });
//             // users.push({ name: 'はってぃー', lat: lat, lng: lng });
//             console.log(user);
//         }
//     });
// });

// console.log(users);

// すべてのユーザーの住所を緯度経度に変換し、users配列に追加するPromiseを作成
                var userPromises = obj2.map(function (user) {
                    return geocodeAddress(geocoder, user.user_address)
                        .then(function (location) {
                            users.push({ name: user.username, lat: location.lat, lng: location.lng });
                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                });

                console.log(userPromises);

                
                // var users = [
                //     { name: 'User 1', lat: 33.5895, lng: 130.4018 },
                //     { name: 'User 2', lat: 33.5907, lng: 130.4020 },
                //     { name: 'User 3', lat: 33.5910, lng: 130.4015 }
                // ];

                // var address = '福岡県福岡市中央区大宮１丁目５−３１';  // 検索する住所

                // 取得した住所を使って検索する
                var address = obj[0].user_address;  // ここで住所を取得
                // var address_2 = obj2[1].user_address;  // ここで住所を取得
console.log(address);

// -------------------
// geocodeAddress(geocoder, address_2, callback)
   // 住所を緯度経度に変換する関数
//    function geocodeAddress(geocoder, address, callback) {
//                 // geocoder.geocodeメソッドを使用して住所を緯度経度に変換
//                 geocoder.geocode({ 'address': address }, function (results, status) {
//                     // ステータスがOKなら緯度経度をコールバック関数に渡す
//                     if (status === 'OK') {
//                         var location = results[0].geometry.location;
//                         callback(location.lat(), location.lng());
//                     } else {
//                         // エラーハンドリング
//                         console.error('Geocode was not successful for the following reason: ' + status);
//                         callback(null, null);
//                     }
//                 });
//             }
            // ------------------------

                geocodeAddress(geocoder, address) 
                .then(function (currentLocation) {
                    var currentLatLng = new google.maps.LatLng(currentLocation.lat, currentLocation.lng);
                    console.log(currentLatLng);
                    map.setCenter(currentLatLng);
                
                
                
                
                // function (lat, lng) {
                //     if (lat && lng) {
                //         var currentLocation = new google.maps.LatLng(lat, lng);  // 緯度経度をLatLngオブジェクトに変換


                        // map.setCenter(currentLocation);  // 地図の中心を現在の位置に設定

                        // console.log(geocoder);

                        // 現在位置にマーカーを追加
                        new google.maps.Marker({
                            position: currentLatLng,
                            map: map,
                            title: 'Current Location'
                        });

                        console.log(currentLatLng);


                        // 半径500m以内👉（今だけ50キロに変更中）のユーザーを検索し、マーカーを追加
                        // var nearbyUsers = getNearbyUsers(users, currentLocation, 1000);
                        // nearbyUsers.forEach(function (user) {
                        //     new google.maps.Marker({
                        //         position: { lat: user.lat, lng: user.lng },
                        //         map: map,
                        //         title: user.name
                        //     });
                        // });
                    // }
                                                // すべての住所変換が完了するのを待つ
                                                Promise.all(userPromises).then(function () {
                            var nearbyUsers = getNearbyUsers(users, currentLatLng, 50000);
                            nearbyUsers.forEach(function (user) {
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