<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>名前を選択</title>
<?php
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
// 研究室所属メンバー
$person = array(
  "安保　建朗",
  "Ghita Athalina",
  "倉嶋　俊",
  "小林　優稀",
  "室井　健一",
  "森田　和貴",
  "渡辺　宇",
  "荒木　香名",
  "柴沢　弘樹",
  "[ゲスト]" // 有事の際は，これで凌ぐ．
  );
// リストの中から，自分の名前を選んでもらう．
for ($i = 0; $i < count($person); $i++) {
  print "<label><input type='radio' name='my_id' value='$person[$i]'>{$person[$i]}<br><br></label>";
}
?>
<input type="submit" value="送信する" onClick="return confirm('名前を再度確認したのち，[OK]を押してください．')" />
</form><br><br><br>
<p><a href="./index.html">TOP</a></p>