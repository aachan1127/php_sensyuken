<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>サイズ調整した画像をアップロードする</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- jqueryの読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


</head>
<body>
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
</body>
</html>


