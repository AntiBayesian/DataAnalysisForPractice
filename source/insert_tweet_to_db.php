 <?php
require_once 'twitteroauth/twitteroauth.php';

define('CONSUMER_KEY', '***');
define('CONSUMER_SECRET', '***');
define('ACCESS_TOKEN', '***');
define('ACCESS_TOKEN_SECRET', '***');

$twitterOAuth = new TwitterOAuth(
    CONSUMER_KEY,
    CONSUMER_SECRET,
    ACCESS_TOKEN,
    ACCESS_TOKEN_SECRET
);

$search_words = '東京 天気';

$param = array(
    "q" => $search_words,
    "lang" => "ja",
    "count" => 100,
    "result_type" => "recent"
);

$json = $twitterOAuth->OAuthRequest(
    "https://api.twitter.com/1.1/search/tweets.json",
    "GET",
    $param
);

$twitter = json_decode($json, true);

// 利用するデータベースファイルの場所(パス)を記述します。
$dsn = 'sqlite:tweet.db';

// PHPのPDOというデータベースを利用するための便利なライブラリを使う設定。
// 詳細についてはPHPの専門書籍を参照してください
$db = new PDO($dsn);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// SQL(データベースからデータ操作を行う言語)を用いて、格納済みの最新ツイート時間を取得する処理。
// なぜこのような処理をするかは、データを重複して格納しないようにするためです。
// 単純にtwitter APIから取得したデータをデータベースに格納する処理を何度も行う場合、
// 以前取得したデータを再度格納して重複してしまう可能性があります。
// そのような重複を防ぐため、現時点でデータベースに格納されているデータのうち最新の時間を取り出し、
// その時間よりも後にツイートされたデータだけを格納するという処理を入れる必要があります。
$query = 'SELECT max(time) last_time FROM tweet';
$prepare = $db->prepare($query);
$prepare->execute();
$result = $prepare->fetch(PDO::FETCH_ASSOC);
if (isset($result['last_time'])){
    $last_time = $result['last_time'];
} else {
    // ツイートを1つも格納していなければ0を割り当てる
    $last_time = 0;
}

// php.iniを設定出来る権限が無い場合などは次のコードからコメント(//の部分)を外して利用する
// php.iniでタイムゾーン設定をしていれば次の記述は不要
// date_default_timezone_set('Asia/Tokyo');

foreach($twitter['statuses'] as $tweet){
    $time = date( "Y-m-d H:i:s", strtotime($tweet['created_at']));
    // ツイートが既に格納済みの最新ツイートよりも新しいかどうかで分岐させる処理
    // 新しい場合のみデータベースに格納する
    if ($time > $last_time) {
        //　SQLを用いて取得したツイートをデータベースに格納する処理
        $query = 'INSERT INTO tweet (id_str, time, text) VALUES (:id_str, :time, :text)';
        $prepare = $db->prepare($query);
        // プリペアード・ステートメントという機能でバグやセキュリティの問題を防ぐ
        // 詳細はセキュリティ系の専門書を参照
        $prepare->bindValue(':id_str', $tweet['id_str'], PDO::PARAM_STR);
        $prepare->bindValue(':time', $time, PDO::PARAM_STR);
        $prepare->bindValue(':text', $tweet['text'], PDO::PARAM_STR);
        $prepare->execute();
    }
}
