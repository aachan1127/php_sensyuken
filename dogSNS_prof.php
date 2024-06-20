
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>dogSNSプロフィール登録画面</title>
</head>

<body>
  <form action="dogSNS_prof_act.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <legend>dogSNSプロフィール登録画面</legend>

      <div>
        わんちゃんの名前: <input type="text" name="dog_name">
      </div>
      <div>
        犬種: <input type="text" name="dog_breeds">
      </div>
      <div>
        年齢: <input type="text" name="dog_birth">
      </div>
      <div>
        住所: <input type="text" name="user_address">
      </div>
      <div>
        仲良くなりたい犬: <input type="text" name="interests">
      </div>
      <div>
        コメント: 
        <!-- <textarea name="" id=""> -->
        <input type="textarea" name="comment">
        <!-- </textarea> -->
      </div>

      <div>
        犬の写真: <input type="file" name="profile_image">
      </div>

      <div>
                飼い主の写真: <input type="file" name="kao">
            </div>


      <div>
        <button>Register</button>
      </div>
      <a href="photo_read.php">ホーム画面</a>
      <a href="dogSNS_prof_edit.php">プロフィール編集</a>
    </fieldset>
  </form>

</body>

</html>