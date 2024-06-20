<?php
session_start();
include("functions.php");
check_session_id();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>dogSNS写真投稿（写真投稿画面）</title>

  <!-- jqueryの読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>

<body>
  <!-- <form action="photo_create.php" method="POST"> -->
    <fieldset>
      <legend>dogSNS写真投稿（写真投稿画面）</legend>
      <a href="photo_read.php">ホーム画面</a>
      <a href="dogSNS_logout.php">logout</a>
      <!-- <div>
        todo: <input type="text" name="todo">
      </div>
      <div>
        deadline: <input type="date" name="deadline">
      </div>
      <div>
        <button>submit</button>
      </div> -->
<div>
      <input type="radio" name="option1" value="0" id="r0" onchange="radioChanged()">
	<label for="r0">調整なし（はみ出した部分はカット）</label><br>
	<input type="radio" name="option1" value="1" id="r1" onchange="radioChanged()">
	<label for="r1">サイズを320×320に自動調整（縦横の比率無視）</label><br>
	<input type="radio" name="option1" value="2" id="r2" onchange="radioChanged()">
	<label for="r2">サイズを320×320に自動調整（縦横の比率維持）</label><br>

	<p><input id="file-name" type="file"></p>
	<p>タイトル: <input id="title" type="text" name="title"></p>
	<p>説明: <textarea id="description" name="description"></textarea></p>
	<p><button onclick="upload()">アップロード</button></p>
	<p><canvas id="canvas"></canvas></p>
	<div id="result"></div>
	<div id="gallery"></div>

	<script src="test.js"></script>
  </div>

    </fieldset>
  </form>

</body>

</html>