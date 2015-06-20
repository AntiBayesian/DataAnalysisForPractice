<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ツイート表示</title>
</head>
<body>
検索結果の最新100件を表示します。
<?php
$dsn = 'sqlite:tweet.db';
$db = new PDO($dsn);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 最新100件のツイート情報を取得するクエリ
// order by timeでtime順にデータを並び替えることが出来ます。
// 何も指定しないと昇順、descを指定すると降順でデータが並び替えられます。
$query = 'select time, text from tweet order by time desc limit 100;';
$stmt = $db->query($query);
$results = $stmt->fetchAll(PDO::FETCH_NUM);

foreach ($results as $key => $value) {
    // <hr>, <br>はHTMLタグ。
    // <hr>で水平線を引き、<br>で改行する
    echo '<hr>'.$value[0].'<br>'.$value[1];
}