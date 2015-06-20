<?php
$dsn = 'sqlite:tweet.db';
$db = new PDO($dsn);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 直近10件分のデータを取得。
$query = 'select date, cnt from series order by date limit 10';

// このようにして日付を変更できるようにするとさらに便利
// 但し、前述(kwic_tweet.php)で説明したように、ユーザからの入力をそのままシステムに適用してはいけません。
// if (isset($_GET["set_date"])) {
//	$query = 'select date, cnt from series where date >= "'.$_GET["set_date"].'"order by date';
// } else {
// 	$query = 'select date, cnt from series order by date limit 10';
// }

$stmt = $db->query($query);
$results = $stmt->fetchAll(PDO::FETCH_NUM);

$db = null;

$data = array();
foreach ($results as $value) {
    $data[] = array('date' => $value[0], 'cnt' => $value[1]);
}
echo header('Content-type: application/json');
echo json_encode($data);
