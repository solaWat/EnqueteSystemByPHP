<?php
/**
 * このプログラムが接続するデータベースの名前
 * @var string
 */
$dbname   = 'enquete_main_2';


/**
 * 初めてデータベースに接続する際のみに用いる．
 * これを使って，$dbname のデータベースを作る．
 * @var string
 */
$pre_dsn  = 'mysql:host=127.0.0.1;charset=utf8';


/**
 * $dbname のデータベースを作成した後は，
 * これを使ってデータベースにアクセスする．
 * @var string
 */
$dsn      = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8mb4';


/**
 * データベースに接続する際の，ユーザーネームとパスワード．
 * 適宜，適切なものを設定すること．
 * @var string
 * @var string
 */
$user     = 'root';
$password = 'root';


/**
 * 接続したデータベースにおけるテーブル名．
 * 各々のデータベース設計については，
 * exMember.php を参照のこと．
 * @var string
 * @var string
 * @var string
 * @var string
 */
$tbname_1   = 'test_vote';
$tbname_2   = 'test_lab_member_info';
$tbname_3   = 'test_order_of_presentation';
$tbname_4   = 'test_order_of_fg';


/**
 * 現在における「年度」の宣言．
 * データベースとやりとりする際にも使われる．
 * 今の所はとりあえず，ベタ打ちとする．
 * @var string
 */
$fiscalyear = '2017';


/**
 * 現在・現地における「日付」と「時間」
 */
date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');
