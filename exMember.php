<?php
$dbname = 'enquete_main_2';//各々の環境で変わります．
$pre_dsn = 'mysql:host=127.0.0.1;charset=utf8';
$dsn = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8mb4';//各々の環境で変わります．
$user = 'root';//各々の環境で変わります．
$password = 'root';//各々の環境で変わります．

$tbname_1 = 'test_vote';
$tbname_2 = 'test_lab_member_info';
$tbname_3 = 'test_order_of_presentation';
$fiscalyear = '2016'; // 今の所はとりあえず，年度に関しては，ベタ打ちとする．

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

try {
  $pre_dbh = new PDO( // databaseがなければ作る．
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
$col_set_tb1 = <<< EOM
  date  date  COMMENT'年月日',
  time  time  COMMENT'時間',
  voter_person_id  varchar(100)  COMMENT'投票者のID',
  types_of_votes  varchar(30)  COMMENT'P or FG ?',
  rank  tinyint unsigned  COMMENT'順位',
  voted_person_id  varchar(100)  COMMENT'被投票者のID'
EOM;
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_1.'('.$col_set_tb1.');');

$col_set_tb2 = <<< EOM
  fiscal_year  year  COMMENT'登録年度',
  studentname  nvarchar(100)  COMMENT'ゼミ所属学生の名前',
  person_id  varchar(100)  COMMENT'ID(年度が異なっても，この値が同じなら，同一人物)'
EOM;
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_2.'('.$col_set_tb2.');');

$col_set_tb3 = <<< EOM
  date  date  COMMENT'年月日',
  time  time  COMMENT'時間',
  attendee_person_id  varchar(100)  COMMENT'出席者のID',
  order_of_presen  tinyint unsigned  COMMENT'発表順'
EOM;
  $dbh->exec('CREATE TABLE IF NOT EXISTS '.$tbname_3.'('.$col_set_tb3.');');

  // 研究室所属メンバーを表示する．
  $sql = 'SELECT * FROM '.$tbname_2.' WHERE fiscal_year = ? ';
  $prepare_memberinfo = $dbh->prepare($sql);
  //$prepare->bindValue(1, $tbname_2, PDO::PARAM_STR);
  $prepare_memberinfo->bindValue(1, $fiscalyear, PDO::PARAM_STR);
  $prepare_memberinfo->execute();



  // POSTが降ってきたら．
  //if (isset($_POST['sort'])) {
  if (!isset($_POST['sort'])) {
      $errors[] = 'Eメールアドレスが送信されていません';
  } elseif ($_POST['sort'] === '') {
      $errors[] = 'Eメールアドレスが入力されていません';
  }else {
  //   # code...
  // }
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
      $attendee_person_id = $_POST['cn'];
      srand(time()); //乱数列初期化．冗長の可能性あり．
      shuffle($attendee_person_id); //　出席者をランダムソートにかけ，発表順を決める．

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
        //$prepare->bindValue(1, $tbname_3, PDO::PARAM_STR);
        $prepare->bindValue(1, $date, PDO::PARAM_STR);
        $prepare->bindValue(2, $time, PDO::PARAM_STR);
        $prepare->bindValue(3, $attendee_person_id[$i], PDO::PARAM_STR);
        $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
        $prepare->execute();
      }
    }
  // }

  // これで済むはずなのに……　<?php include 'current_exOrder.php';
$sql = <<< EOM
  SELECT studentname
  FROM  {$tbname_2}
  LEFT JOIN {$tbname_3}
  ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
  WHERE {$tbname_3}.date = ?
   AND time = (SELECT MAX(time) FROM {$tbname_3} WHERE date = ?)
  ORDER BY {$tbname_3}.order_of_presen;
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $date, PDO::PARAM_STR);
  $prepare->bindValue(2, $date, PDO::PARAM_STR);
  $prepare->execute();

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

<form method="post" action="exMember.php">
<h2>○出席者を選んでください</h2>
<div style="background: #ddf; width:200px; border: 1px double #CC0000; height:100％; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px;">

<!--  残すやつ -->
<?php foreach ($prepare_memberinfo as $row): ?>
  <?php $name   = $row['studentname'];?>
  <?php $id = $row['person_id'];?>
  <label>
    <input type='checkbox' name='cn[]' value='<?=h($id)?>' checked><?=h($name)?>
    <br><br>
  </label>
<?php endforeach; ?>
<!--  -->

<!-- 残すやつ -->
</div><br>
 <!-- 出席者から今日の発表順がソートされる時点で，これより前の投票結果を削除する．投票の反映は上書きではなく，足し込みのため． -->
<input type="submit" name="sort" value="　発表順を決める　" >
</form>
<!--  -->

<h2>○今日の発表順はこちら</h2>

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


<br>
<!-- 直下のurlをいじると，ベルの時間とテキストのデフォルト表示を変えられる．ベルの時間の実際に鳴る時間は，コードもいじる必要がある． -->
<h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
<h4><a href= request_exOrder.php ><font color="blue"> 発表順を編集 </font>
<h4><a href= index.html ><font color="green"> TOP </font>
</a><h4>
<br><br><br>
</body>
</html
