<?php

try {
  session_start();

  //session_unset(); // 特定のセッション変数のみを消したい場合に使うのが良いらしい．
  // 以下の処理は，冗長という意見がある．
  $_SESSION = array(); // セッション変数を，空の配列で初期化
  setcookie(session_name(), '', time() - 3600, '/'); // ブラウザのクッキーを削除
  session_destroy(); // サーバ側にあるセッションIDの破棄．

} catch (Exception $e) {
  header('Content-Type: text/plain; charset=UTF-8', true, 500);
  echo 'エラー!: '.$e->getMessage().'<br/>';
  die();
}
header('Content-Type: text/html; charset=utf-8');
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>セッションCookieの消去</title>

<p>消去に成功しました．</p>
<p><a href="./index.html">TOP</a></p>
