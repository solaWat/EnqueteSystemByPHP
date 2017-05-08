<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {
  /**
   * セッション関係の処理．
   * 特筆すべきは session_regenerate_id(); の処理．
   */
  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
  session_start();
  session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．

  // inputName.php」で選ばれた名前を保持．
  if (isset($_POST['my_id'])) {
      $_SESSION['my_id'] = $_POST['my_id'];
  }
  // これがあると，セッションがとってあった場合，exitになってしまう．
  // else {
  //   echo "あなたの名前がわかりません．";
  //   exit;
  // }
  $token = $_SESSION['token'];
  $fromSession = $_SESSION['my_id'];

  /**
   * 後の処理で使い回す $dbh を作る．
   */
  $dbh = new PDO( // tableがなければ作る．
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
   * セッションを読み込んで，idと名前を照合し，保持する．
   */
  $sql = <<< EOM
    SELECT studentname
    FROM {$tbname_2}
    WHERE person_id = ?
    AND   fiscal_year = ?
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $fromSession, PDO::PARAM_STR);
  $prepare->bindValue(2, $fiscalyear, PDO::PARAM_STR);
  $prepare->execute();

  foreach ($prepare as $row) {
      $masters_name = $row['studentname'];
  }

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * プレゼン用
   */
  $sql_vote = <<< EOM
    SELECT studentname, person_id
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
    AND   time = (
      SELECT MAX(time)
      FROM {$tbname_3}
      WHERE date = ? )
    ORDER BY {$tbname_3}.order_of_presen;
EOM;
  $prepare_order_pr = $dbh->prepare($sql_vote);
  $prepare_order_pr->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_pr->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_pr->execute();

  foreach ($prepare_order_pr as $key => $row) {
    $name_text_PRESEN[$key]      = $row['studentname'];
    $id_text_PRESEN[$key]        = $row['person_id'];
  }

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * ファシグラ用
   */
  $sql_vote_fg = <<< EOM
    SELECT studentname, person_id
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    WHERE {$tbname_4}.date = ?
    AND   time = (
      SELECT MAX(time)
      FROM {$tbname_4}
      WHERE date = ? )
    ORDER BY {$tbname_4}.order_of_fg;
EOM;
  $prepare_order_fg = $dbh->prepare($sql_vote_fg);
  $prepare_order_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_fg->execute();

  foreach ($prepare_order_fg as $key => $row) {
    $name_text_FG[$key]      = $row['studentname'];
    $id_text_FG[$key]        = $row['person_id'];
  }

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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>

<h4>こんにちは <font color='#696969'><big><?=h($masters_name)?></big></font> さん</h4>

<!-- ゼミにおいて，投票の際に意識する評価指標の項目を列挙する -->
<p>
  ○投票の際，以下のポイントを意識してください．<br />
  <!-- <font color=red>　＜プレゼンテーション＞</font><br />
  <font color=red>　・その論文を読んでみたくなったか．</font><br />
  <font color=red>　・ etc... </font><br />
  <font color=red>　＜ファシとグラ＞</font><br />
  <font color=red>　・ etc... </font><br /> -->
<!-- </p> -->
<table>
  <tr>
    <td>
      <font color=red><b>　＜プレゼンテーション＞</font>
    </td>
    <td>
      <font color=red><b>　＜ファシとグラ＞</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・制限時間が守られていたか（過ぎた後，発表を続けていないか）　</font>
    </td>
    <td>
      <font color=red><b>　・意見（「発表」を含む）の整理ができていたか　</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・意見・質問に対して，根拠ある返答がはっきりとなされていたか　</font>
    </td>
    <td>
      <font color=red><b>　　　（例．発言の補足のための深掘り・見やすいグラフィック（色，字，文，絵）・意見の書き出し）　</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・発表内容に対して興味深いと思える点が1つでもあったか　</font>
    </td>
    <td>
      <font color=red><b>　・場に対して，討論が発生するような問題（議題）提起ができていたか　</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・発表者の態度を心がけていたか（例．貧乏ゆすり・うつむき・表情）　</font>
    </td>
    <td>
      <font color=red><b>　・討論の切れ目が見極められていたか　</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・声が大きく，抑揚がついていたか　</font>
    </td>
    <td>
      <font color=red><b>　・（グラフィックにおいて，フレームワークが活用できていたか）　</font>
    </td>
  </tr>
  <tr>
    <td>
      <font color=red><b>　・話にリズム・キレ・ストーリー性があったか　</font>
    </td>
    <td>
      <font color=red><b>　　</font>
    </td>
  </tr>
</table>
</p>

<p>○こちらの2つの表から，それぞれ投票を行ってください．</p>
<form method="post" action="mainVote.php">

<!-- 2つのテーブルを並列させるための透明テーブル． -->
<table>
  <tr>
    <td>
      <table border='1' cellpadding='8' style='background:#F0F8FF'>
        <caption>
          プレゼンテーション
        </caption>
        <tr>
          <th>
            1位
          </th>
          <th>
            2位
          </th>
          <th>
            3位
          </th>
          <th>

          </th>
        </tr>
        <?php $j = 0; $k = 0;?>
        <?php foreach ($name_text_PRESEN as $row): ?>
          <?php $j = $j + 1; ?>
          <?php if (strpos($id_text_PRESEN[$k], $fromSession) !== false) { ?>
          <tr>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> disabled>
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> disabled>
            </td>
            <td>
              <?=h($j)?>.<?=h($name_text_PRESEN[$k])?>
            </td>
          </tr>
          <?php } else { ?>
          <tr>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='cn<?=$h?>' value=<?=$id_text_PRESEN[$k] ?> >
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> >
            </td>
            <td>
              <?=h($j)?>.<?=h($name_text_PRESEN[$k])?>
            </td>
          </tr>
          <?php } ?>
          <?php $k = $k + 1; ?>
        <?php endforeach; ?>
      </table>
    </td>

    <td>
      <table border="1" cellpadding="8" style="background:#F5F5F5">
        <caption>
          ファシとグラ
        </caption>
        <tr>
          <th>

          </th>
          <th>
            1位
          </th>
          <th>
            2位
          </th>
          <th>
            3位
          </th>
        </tr>
        <?php $k = 0;?>
        <?php foreach ($name_text_FG as $row): ?>

          <?php if (strpos($id_text_FG[$k], $fromSession) !== false) { ?>
          <tr>
            <td>
              <?=h($name_text_FG[$k])?>
            </td>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> disabled>
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> disabled>
            </td>
          </tr>
          <?php } else { ?>
          <tr>
            <td>
              <?=h($name_text_FG[$k])?>
            </td>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> >
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> >
            </td>
          </tr>
          <?php } ?>
          <?php $k = $k + 1; ?>
        <?php endforeach; ?>
      </table>
    </td>
  </tr>
</table>

<br>
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" name="submit" value="投票する" >
</form>

<!-- フォームを並列したい時に使うもの．
<div style="display:inline-flex">
<form><input type="text"><input type="submit"></form>
<form><input type="text"><input type="submit"></form>
</div> -->

<?php
// 投票ボタン
if ($_POST['submit']) {

  //ラジオボタン選択のエラー表示用
  if ($_POST['cn1'] == null or $_POST['cn2'] == null or $_POST['cn3'] == null) {
      $error_msg = "<h4><font color='red'>※プレゼンテーションの全ての順位を埋めてください</font></h4>";
      echo $error_msg;
      exit; // エラーを検知すると，投票はデータベースに書き込まれず，集計結果も見に行けなくなる．
  }
  if ($_POST['co1'] == null or $_POST['co2'] == null or $_POST['co3'] == null) {
      $error_msg = "<h4><font color='red'>※ファシリテーション＆グラフィックスの全ての順位を埋めてください</font></h4>";
      echo $error_msg;
      exit; //die関数にしてもいいかも．
  }
  if ($_POST['cn1'] == $_POST['cn2'] || $_POST['cn2'] == $_POST['cn3'] || $_POST['cn1'] == $_POST['cn3']) {
      $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（プレゼンテーション）</font></h4>";
      echo $error_msg;
      exit;
  }
  if ($_POST['co1'] == $_POST['co2'] || $_POST['co2'] == $_POST['co3'] || $_POST['co1'] == $_POST['co3']) {
      $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（ファシとグラ）</font></h4>";
      echo $error_msg;
      exit;
  }

  // セッションを開始する
  if (!isset($_SESSION)) {
      session_start();
  }
  // セッションに入れておいたトークンを取得
  $session_token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
  // POSTの値からトークンを取得
  $token = isset($_POST['token']) ? $_POST['token'] : '';

  // トークンがない場合は不正扱い
  if ($token === '') {
      echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに入れたトークンとPOSTされたトークンの比較
  if ($token !== $session_token) {
      echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに保存しておいたトークンの削除
  unset($_SESSION['token']);

  echo "<h3><font color='blue'>投票に成功しました．集計結果を見に行きましょう．</font></h3>";

  try {
    $sql = <<< EOM
      DELETE
      FROM {$tbname_1}
      where date = ?
      AND voter_person_id = ?
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $fromSession, PDO::PARAM_STR);
    $prepare->execute();

    $sql_insert_vote = <<< EOM
      INSERT INTO {$tbname_1}
      ( date,
        time,
        voter_person_id,
        types_of_votes,
        rank,
        voted_person_id )
      VALUES (?, ?, ?, ?, ?, ?)
EOM;
    for ($i = 1; $i < 4 ; ++$i) {
      $voted_person_id = $_POST["cn$i"];
      $prepare = $dbh->prepare($sql_insert_vote);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $fromSession, PDO::PARAM_STR);
      $prepare->bindValue(4, 'P', PDO::PARAM_STR);
      $prepare->bindValue(5, $i, PDO::PARAM_STR);
      $prepare->bindValue(6, $voted_person_id, PDO::PARAM_INT);
      $prepare->execute();
    }
    for ($i = 1; $i < 4 ; ++$i) {
      $voted_person_id = $_POST["co$i"];
      $prepare = $dbh->prepare($sql_insert_vote);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $fromSession, PDO::PARAM_STR);
      $prepare->bindValue(4, 'FG', PDO::PARAM_STR);
      $prepare->bindValue(5, $i, PDO::PARAM_STR);
      $prepare->bindValue(6, $voted_person_id, PDO::PARAM_INT);
      $prepare->execute();
    }
  } catch (Exception $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    echo 'エラー!: '.$e->getMessage().'<br/>';
    die();
  }
}
?>

<br>
<h3><a href= resultVis.php > 集計結果を見る </a></h3><br><br>
<a href= index.html > TOP </a>
</body></div>
</html>
