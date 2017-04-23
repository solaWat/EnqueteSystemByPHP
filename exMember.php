<?php
$dbname   = 'enquete_main_2';//各々の環境で変わります．
$pre_dsn  = 'mysql:host=127.0.0.1;charset=utf8';
$dsn      = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8mb4';//各々の環境で変わります．
$user     = 'root';//各々の環境で変わります．
$password = 'root';//各々の環境で変わります．

$tbname_1   = 'test_vote';
$tbname_2   = 'test_lab_member_info';
$tbname_3   = 'test_order_of_presentation';
$tbname_4   = 'test_order_of_fg';
$fiscalyear = '2017'; // 今の所はとりあえず，年度に関しては，ベタ打ちとする．

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

try {
  /**
   * まず，DBを登録する．
   */
  $pre_dbh = new PDO(
    $pre_dsn,
    $user,
    $password,
    array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    )
  );
  $pre_dbh->exec('CREATE DATABASE IF NOT EXISTS '.$dbname);

  /**
   * DBに，TABLEを4つ登録する．
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
  $col_set_tb1 = <<< EOM
    date  date  COMMENT'年月日',
    time  time  COMMENT'時間',
    voter_person_id  varchar(100)  COMMENT'投票者のID',
    types_of_votes  varchar(30)  COMMENT'P or FG ?',
    rank  tinyint unsigned  COMMENT'順位',
    voted_person_id  varchar(100)  COMMENT'被投票者のID'
EOM;
  $col_set_tb2 = <<< EOM
    fiscal_year  year  COMMENT'登録年度',
    studentname  nvarchar(100)  COMMENT'ゼミ所属学生の名前',
    person_id  varchar(100)  COMMENT'ID(年度が異なっても，この値が同じなら，同一人物)'
EOM;
  $col_set_tb3 = <<< EOM
    date  date  COMMENT'年月日',
    time  time  COMMENT'時間',
    attendee_person_id  varchar(100)  COMMENT'参加者のID',
    order_of_presen  tinyint unsigned  COMMENT'発表順'
EOM;
  $col_set_tb4 = <<< EOM
    date  date  COMMENT'年月日',
    time  time  COMMENT'時間',
    attendee_person_id  varchar(100)  COMMENT'参加者のID',
    order_of_fg  tinyint unsigned  COMMENT'担当順'
EOM;
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_1.'('.$col_set_tb1.');');
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_2.'('.$col_set_tb2.');');
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_3.'('.$col_set_tb3.');');
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_4.'('.$col_set_tb4.');');

  /**
   * 研究室所属メンバーを表示する．
   */
  // $sql = 'SELECT * FROM '.$tbname_2.' WHERE fiscal_year = ? ';
  $sql = <<< EOM
    SELECT studentname, person_id
    FROM {$tbname_2}
    WHERE fiscal_year = ?
EOM;
  $prepare_memberinfo = $dbh->prepare($sql);
  $prepare_memberinfo->bindValue(1, $fiscalyear, PDO::PARAM_STR);
  $prepare_memberinfo->execute();

  $prepare_memberinfo_fg = $dbh->prepare($sql);
  $prepare_memberinfo_fg->bindValue(1, $fiscalyear, PDO::PARAM_STR);
  $prepare_memberinfo_fg->execute();

  /**
   *  POST（ランダムで決める）が降ってきた際の処理
   */
  //if (isset($_POST['sort'])) {
  if (!isset($_POST['sort_pr'])) {
      $errors[] = '送信されていません';
  } elseif ($_POST['sort_pr'] === '') {
      $errors[] = '入力されていません';
  }else {
    // if (!isset($_POST['cn'])) {
    //     $attendee_person_id = null;
    // } elseif (!is_string($_POST['cn'])) {
    //     $attendee_person_id = false;
    // } else {
    //     $attendee_person_id = $_POST['cn'];
    // }
    // 二行下までの処理は上記と等価である．
    // $attendee_person_id = filter_input(INPUT_POST, 'cn');
    // if (is_string($attendee_person_id)) {
      /* 文字列として送信されてきた場合のみ実行したい処理 */
      $attendee_person_id = $_POST['cn_pr'];
      srand(time()); //乱数列初期化．冗長の可能性あり．
      shuffle($attendee_person_id); //　参加者をランダムソートにかけ，発表順を決める．

      // すでにその日の発表順が入っている場合は，それをまずDELETEする．
      $sql = 'DELETE FROM '.$tbname_3.' WHERE date = ?';
      $prepare = $dbh->prepare($sql);
      //$prepare->bindValue(1, $tbname_3, PDO::PARAM_STR);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->execute();

      // 発表順を入れる．
      for ($i = 0; $i < count($attendee_person_id); $i++) {
        $j = $i + 1;
        $sql = 'INSERT INTO '.$tbname_3.'(date, time, attendee_person_id, order_of_presen) VALUES (?, ?, ?, ?)';
        $prepare = $dbh->prepare($sql);
        $prepare->bindValue(1, $date, PDO::PARAM_STR);
        $prepare->bindValue(2, $time, PDO::PARAM_STR);
        $prepare->bindValue(3, $attendee_person_id[$i], PDO::PARAM_STR);
        $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
        $prepare->execute();
      }
    }
  // }

  if (!isset($_POST['sort_fg'])) {
      $errors[] = '送信されていません';
  } elseif ($_POST['sort_fg'] === '') {
      $errors[] = '入力されていません';
  }else {
    $attendee_person_id = $_POST['cn_fg'];
    srand(time()); //乱数列初期化．冗長の可能性あり．
    shuffle($attendee_person_id); //　参加者をランダムソートにかけ，順番を決める．

    // すでにその日の順番が入っている場合は，それをまずDELETEする．
    $sql = 'DELETE FROM '.$tbname_4.' WHERE date = ?';
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->execute();

    // ファシグラの順番を入れる．
    for ($i = 0; $i < count($attendee_person_id); $i++) {
      $j = $i + 1;
      $sql = 'INSERT INTO '.$tbname_4.'(date, time, attendee_person_id, order_of_fg) VALUES (?, ?, ?, ?)';
      $prepare = $dbh->prepare($sql);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $attendee_person_id[$i], PDO::PARAM_STR);
      $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
      $prepare->execute();
    }
  }

  /**
   * POST（発表順をアレンジしてファシグラ順に）が降ってきた際の処理
   */
  if (!isset($_POST['arrange'])) {
    $errors[] = '送信されていません';
  } elseif ($_POST['arrange'] === '') {
    $errors[] = '入力されていません';
  }else {

    // すでにその日のファシグラ担当順が入っている場合は，それをまずDELETEする
    $sql = 'DELETE FROM '.$tbname_4.' WHERE date = ?';
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->execute();

    $sql_for_arrange = <<< EOM
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
    $prepare_presen_order = $dbh->prepare($sql_for_arrange);
    $prepare_presen_order->bindValue(1, $date, PDO::PARAM_STR);
    $prepare_presen_order->bindValue(2, $date, PDO::PARAM_STR);
    $prepare_presen_order->execute();

    foreach ($prepare_presen_order as $key => $row) {
      // $name_text_PRESEN[$key]      = $row['studentname'];
      $order_id_PRESEN[$key]        = $row['person_id'];
    }

    function change_order_for_FG($array){
      $person_fg  = $array;
      $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
      $person_two = $person_fg[1];
      for ($i = 0; $i < count($person_fg); ++$i) {
          if (($person_fg[$i + 2]) == null) {
              if ($person_fg[$i + 1] == null) {
                  $person_fg[$i] = $person_two;
              } else {
                  $person_fg[$i] = $person_one;
              }
          } else {
              $person_fg[$i] = $person_fg[$i + 2];
          }
      }
      return $person_fg;
    }

    // $name_text_FG = change_order_for_FG($name_text_PRESEN);
    $order_id_FG   = change_order_for_FG($order_id_PRESEN);

    // 発表順を入れる．
    for ($i = 0; $i < count($order_id_FG); $i++) {
      $j = $i + 1;
      $sql = 'INSERT INTO '.$tbname_4.'(date, time, attendee_person_id, order_of_fg) VALUES (?, ?, ?, ?)';
      $prepare = $dbh->prepare($sql);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $order_id_FG[$i], PDO::PARAM_STR);
      $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
      $prepare->execute();
    }
  }

  /**
   * 現在の順番をDBから吸い出す．
   */
  // これで済むはずなのに……　<?php include 'current_exOrder.php';
  // プレゼン用
  $sql = <<< EOM
    SELECT studentname
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
     AND time = (SELECT MAX(time) FROM {$tbname_3} WHERE date = ? )
    ORDER BY {$tbname_3}.order_of_presen;
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $date, PDO::PARAM_STR);
  $prepare->bindValue(2, $date, PDO::PARAM_STR);
  $prepare->execute();

  // ファシグラ用（プレゼン用との違いは，tablenameだけ．）
  $sql_fg = <<< EOM
    SELECT studentname
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    WHERE {$tbname_4}.date = ?
     AND time = (SELECT MAX(time) FROM {$tbname_4} WHERE date = ? )
    ORDER BY {$tbname_4}.order_of_fg;
EOM;
  $prepare_fg = $dbh->prepare($sql_fg);
  $prepare_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_fg->execute();

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
  <title>発表順</title>
  <div style="background-color: #cff;">
</head>
<body>
  <h2>○研究室所属メンバーの中から，選んでください</h2>
  <p>
    ※何も選択せずボタンを押した場合，本日の順番データが全て削除されます．
  </p>
  <!-- <table>
    <tr>
        <td>機能を選択してください</td>
        <form action="A.php">
        <td>
            <input type="submit" value="機能A" />
        </td>
        </form>
        <form action="B.php">
        <td>
            <input type="submit" value="機能B" />
        </td>
        </form>
    </tr>
  </table> -->

  <table>
    <tr>
      <td>
        プレゼンテーション
        <form method="post" action="exMember.php">
          <div style="background: #ddf; width:200px; border: 1px double #CC0000; height:100％; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px;">
            <?php foreach ($prepare_memberinfo as $row): ?>
              <?php $name   = $row['studentname'];?>
              <?php $id = $row['person_id'];?>
            <label>
              <input type='checkbox' name='cn_pr[]' value='<?=h($id)?>'><?=h($name)?>
              <br><br>
            </label>
            <?php endforeach; ?>
          </div><br>
          <input type="submit" name="sort_pr" value="　pランダムで順番を決める　" >
        </form>
      </td>
      <td>
        　　
      </td>
      <td>
        ファシグラ
        <form method="post" action="exMember.php">
          <div style="background: #ddf; width:200px; border: 1px double #CC0000; height:100％; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px;">
            <?php foreach ($prepare_memberinfo_fg as $row): ?>
              <?php $name   = $row['studentname'];?>
              <?php $id = $row['person_id'];?>
            <label>
              <input type='checkbox' name='cn_fg[]' value='<?=h($id)?>'><?=h($name)?>
              <br><br>
            </label>
            <?php endforeach; ?>
          </div><br>
          <input type="submit" name="sort_fg" value="　fランダムで順番で決める　" >
          </form>
      </td>
    </tr>
  </table>

  <h2>○今日の発表順はこちら</h2>
  <table>
    <tr>
      <td>
        プレゼンテーション
        <!-- これで済むはずなのに…… include 'current_exOrder.php'; -->
        <table border='1' cellpadding='5' style='background:#F0F8FF'>
          <?php $i = 1; ?>
          <?php foreach ($prepare as $row): ?>
            <tr>
              <td><?=h($i) ?></td>
              <td><?=h($row['studentname'])?></td>
            </tr>
            <?php $i = $i + 1; ?>
          <?php endforeach; ?>
        </table>
      </td>
      <td>
        　　
      </td>
      <td>
        ファシグラ
        <table border='1' cellpadding='5' style='background:#F5F5F5'>
          <?php $i = 1; ?>
          <?php foreach ($prepare_fg as $row): ?>
            <tr>
              <td><?=h($i) ?></td>
              <td><?=h($row['studentname'])?></td>
            </tr>
            <?php $i = $i + 1; ?>
          <?php endforeach; ?>
        </table>
      </td>
    </tr>
  </table><br /><br />

  <form method="post" action="exMember.php">
    <input type="submit" name="arrange" value="　発表順の2つ先の名前を，ファシグラ担当にする　" />
  </form>
  <br>
  <!-- 直下のurlをいじると，ベルの時間とテキストのデフォルト表示を変えられる．ベルの時間の実際に鳴る時間は，コードもいじる必要がある． -->
  <h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
  <h4><a href= request_exOrder.php ><font color="blue"> 順番を細かく編集 </font>
  <h4><a href= index.html ><font color="green"> TOP </font>
  </a><h4>
  <br><br><br>
</body>
</html
