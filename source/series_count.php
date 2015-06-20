<?php
$dsn = 'sqlite:tweet.db';
$db = new PDO($dsn);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = 'select substr(time, 1, 10), count(*) from tweet group by substr(time, 1, 10)';
$stmt = $db->query($query);
$results = $stmt->fetchAll(PDO::FETCH_NUM);

foreach ($results as $key => $value) {
    $query = 'INSERT INTO series (date, cnt) VALUES (:date, :cnt)';
    $prepare = $db->prepare($query);
    $prepare->bindValue(':date', $value[0], PDO::PARAM_STR);
    $prepare->bindValue(':cnt', $value[1], PDO::PARAM_INT);
    $prepare->execute();
}

$db = null;
