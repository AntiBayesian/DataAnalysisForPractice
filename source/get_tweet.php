 <?php
// <-これ以降の文字列はプログラムの挙動に影響を与えない。「コメント」と呼ばれる、コードに対して注記するための記法

// twitterAPIを利用するためには正式に利用登録を行った利用者であることを
// twitter側に伝える「認証処理」が必要です。
// ここではtwitter用の認証ライブラリであるtwitteroauthを利用します。
// ライブラリとは便利な機能を使えるようにまとめておいたプログラムのことです。
// ライブラリを利用することで様々な機能を実装の手間を掛けずに簡単に使えるようになります。
require_once 'twitteroauth/twitteroauth.php';

// ご自分のCONSUMER_KEY他を設定して下さい
define('CONSUMER_KEY', '***');
define('CONSUMER_SECRET', '***');
define('ACCESS_TOKEN', '***');
define('ACCESS_TOKEN_SECRET', '***');

// twitteroauthで認証するためのCONSUMER_KEYなどの情報を設定。
$twitterOAuth = new TwitterOAuth(
    CONSUMER_KEY,
    CONSUMER_SECRET,
    ACCESS_TOKEN,
    ACCESS_TOKEN_SECRET
);

// 検索キーワードを設定
// キーワードをスペースで繋げると、両方のキーワードを含むツイートを収集します。
// キーワードをorで繋ぐと、キーワードのどちらかだけでも含むツイートを収集します。
// 東京の天気に関するツイートを取集したい場合は、両方のキーワードを含んで欲しいため、スペースで繋いでいます。
$search_words = '東京 天気';

// twitter APIに渡すパラメタを指定。
// q : 検索キーワード
// lang : 言語設定。"ja"を設定することで日本語ツイートのみ取得
// count : ツイートを最大何件取得するかの設定
// result_type : 取得順番タイプの設定。recentで最新順、popularで人気順
$param = array(
    "q"=>$search_words,
    "lang"=>"ja",
    "count"=>10,
    "result_type"=>"recent");

// twitter APIを利用してパラメタの指定通りにデータを取得する処理。
// 本来ならここで認証処理が入りますが、twitteroauthが適切に行ってくれます。
// https://api.twitter.com/1.1/search/tweets.json はtwitter APIのURLです。
// このURLにparamで設定した条件でツイートを取得したいと要望を送る（これをリクエストと言います）ことでツイートを取得出来ます。
$json = $twitterOAuth->OAuthRequest(
    "https://api.twitter.com/1.1/search/tweets.json",
    "GET",
    $param);

// ツイート情報を扱いやすいように連想配列という形式に変換して$twitterの中に格納
$twitter = json_decode($json, true);

// var_dump関数を使うと変数の中身を確認できます。
// twitter APIからどのようなデータが取得できるのか一度眺めてみてください。
// この出力結果を見ると、取得したデータは多岐に渡り、ツイート内容だけではなく、
// ユーザ名や時間、ツイートした地点についてもデータを取得できることがわかります。これらを有効活用しましょう。
// var_dump($json);
// var_dump($twitter);

// 検索にヒットした複数のツイートを一つずつ取り出し、ユーザ名とツイート内容、投稿時間を表示
foreach($twitter['statuses'] as $tweet){
    echo $tweet['user']['name']; // ユーザ名
    echo $tweet['text']; //ツイート内容
    echo date( "Y-m-d H:i:s", strtotime($tweet['created_at'])); //　投稿時間。但し、twitterから直接渡される投稿時間は見辛いので整形している
    echo "\r\n"; // 出力結果を見易いように改行
}
