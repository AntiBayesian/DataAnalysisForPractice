<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>KWIC検索</title>
</head>
<body>

検索結果の最新100件を表示します。
<form action="kwic_tweet.php" method="get">
  <input type="text" name="word" placeholder="検索語を入力してください">
  <input type="submit" value="KWIC検索">
</form>

<?php
$dsn = 'sqlite:tweet.db';
$db = new PDO($dsn);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 検索ボックスに検索ワードが入れられていたかどうかで分岐
if (isset($_GET['word'])) {// 検索ボックスに検索ワードが入っている場合の処理
    $kwic = $_GET['word'];

    // 検索ワードにヒットした件数を取得。
    // SQLはwhereで絞り込み条件を設定できる。
    // text like "%AAA%"で、AAAを含むtextを取得する。
    $query = 'select count(*) from tweet where text like :kwic ;';
    $stmt = $db->query($query);
    $stmt->bindValue(':kwic', '%' . preg_replace('/(?=[!_%])/', '!', $kwic) . '%', PDO::PARAM_STR);

    $cnt = $stmt->fetch(PDO::FETCH_NUM);
    echo '検索ワード：'.$kwic.', 総ヒット件数:'.$cnt[0];

    // 検索ワードにヒットしたツイート情報を取得するクエリ
    $query = 'select time, id_str, text from tweet where text like :kwic order by time desc limit 100;';
    $stmt = $db->query($query);
    $stmt->bindValue(':kwic', '%' . preg_replace('/(?=[!_%])/', '!', $kwic) . '%', PDO::PARAM_STR);
} else {// 検索ボックスに検索ワードが入っていない場合の処理
    $query = 'select time, id_str, text from tweet order by time desc limit 100;';
    $stmt = $db->query($query);
}

$results = $stmt->fetchAll(PDO::FETCH_NUM);

// 受け取った検索ワードをそのまま利用するのではなく、htmlspecialcharsというHTMLタグを除去する関数に通す。
// これに限らず、セキュリティ対策のため、必ずユーザ入力をそのまま利用しないこと！
foreach ($results as $key => $value) {
    echo '<hr>'.htmlspecialchars($value[0]) .', '. htmlspecialchars($value[1]) .'<br>'. htmlspecialchars($value[2]);
}
