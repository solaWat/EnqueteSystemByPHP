<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>名前を選択</title>
<?php
// ini_set( 'session.save_path', '/var/tmp_enqueteSystem' );

ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．

session_start(); // session_start() は、セッションを作成します。 もしくは、リクエスト上で GET, POST またはクッキーにより渡されたセッション ID に基づき現在のセッションを復帰します。

// トークンを発行する
$token = md5(uniqid(rand(), true));
// トークンをセッションに保存
$_SESSION['token'] = $token;

if (isset($_SESSION['my_id'])){ // 以前のセッション登録したことがある場合
    header('Location: mainVote.php'); // 即，投票ページに飛ぶ．
	exit();
}
?>

<!-- 自分の名前の登録は，遷移先で行われる． -->
<form action="mainVote.php" method="post"> 
<?php
echo "<h3>あなたの名前を教えてください．</h3>";

try{
  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query("USE enquete_main");

  // "fiscal_year"に関しては，後で，フロントサイドからトグル？などで「年度」を選択できるようにしたい． 
  $st = $dbh->query("SELECT * FROM TestA_2_lab_member_info WHERE fiscal_year = '2016'"); 

  foreach ($st as $row) {
    $name = $row['studentname'];
    $id = $row['person_id'];
    print "<label><input type='radio' name='my_id' value='$id' checked>{$name}<br><br></label>";
  }
}catch (PDOException $e) {
    print "エラー!: " . $e->getMessage() . "<br/>";
    die();
}

?>
<input type="submit" value="送信する" onClick="return confirm('名前を再度確認したのち，[OK]を押してください．')" />
</form><br><br><br>
<p><a href="./index.html">TOP</a></p>