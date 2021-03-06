<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {

  /**
   * セッションクッキーの設定やトークンの付与など
   */
  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
  session_start(); // session_start() は、セッションを作成します。 もしくは、リクエスト上で GET, POST またはクッキーにより渡されたセッション ID に基づき現在のセッションを復帰します。

  // トークンを発行する
  $token = md5(uniqid(rand(), true));
  // トークンをセッションに保存
  $_SESSION['token'] = $token;

  // 以前にセッション登録したことがある場合，
  // 即，投票ページ(mainVote.php)に飛ぶ．
  if (isset($_SESSION['my_id'])) {
      header('Location: mainVote.php');
      exit();
  }

  /**
   * 後の処理で使い回す $dbh を作る．
   */
  $dbh = new PDO(
    $dsn,
    $user,
    $password,
    array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    )
  );

  /**
  * 研究室現所属者の情報を，DBにアクセスして保持する．
  */
  $sql = <<< EOM
   SELECT studentname, person_id
   FROM {$tbname_2}
   WHERE fiscal_year = ?
EOM;
  $prepare_memberinfo = $dbh->prepare($sql);
  $prepare_memberinfo->bindValue(1, $fiscalyear, PDO::PARAM_STR);
  $prepare_memberinfo->execute();

} catch (Exception $e) {
  header('Content-Type: text/plain; charset=UTF-8', true, 500);
  echo 'エラー!: '.$e->getMessage().'<br/>';
  die();
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
header('Content-Type: text/html; charset=utf-8');
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>名前を選択</title>
<h3>あなたの名前を教えてください．</h3>

<!-- 自分の名前の登録は，遷移先で行われる． -->
<form action="mainVote.php" method="post">
  <?php foreach ($prepare_memberinfo as $row): ?>
  <?php $name   = $row['studentname'];?>
  <?php $id = $row['person_id'];?>
      <label>
        <input type='radio' name='my_id' value='<?=h($id)?>' checked><?=h($name)?>
        <br><br>
      </label>
  <?php endforeach; ?>

<input type="submit" value="送信する" onClick="return confirm('名前を再度確認したのち，[OK]を押してください．')" />
</form><br><br><br>
<p><a href="./index.html">TOP</a></p>
