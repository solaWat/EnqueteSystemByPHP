<?php
$dbname   = 'enquete_main_2';//各々の環境で変わります．
$pre_dsn  = 'mysql:host=127.0.0.1;charset=utf8';
$dsn      = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8mb4';//各々の環境で変わります．
$user     = 'root';//各々の環境で変わります．
$password = 'root';//各々の環境で変わります．

$tbname_1   = 'test_vote';
$tbname_2   = 'test_lab_member_info';
$tbname_3   = 'test_order_of_presentation';
$fiscalyear = '2017'; // 今の所はとりあえず，年度に関しては，ベタ打ちとする．


date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

try {
  if ($_POST['delete']) { // デリートが押されたら．
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
    $sql = <<< EOM
      SELECT attendee_person_id
      FROM {$tbname_3}
      WHERE date = ?
      AND   time = (SELECT MAX(time)
                    FROM {$tbname_3}
                    WHERE date = ? ) ;
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $date, PDO::PARAM_STR);
    $prepare->execute();

    foreach ($prepare as $row) {
        $newOrder[] = $row['attendee_person_id'];
    }

    for ($i = 0; $i < count($newOrder) - 1; ++$i) {
        $j   = $i + 1;
        $sql_delete_2 = <<< EOM
          INSERT INTO {$tbname_3} (date, time, attendee_person_id, order_of_presen)
          VALUES (?, ?, ?, ?) ;
EOM;
    $prepare = $dbh->prepare($sql_delete_2);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $time, PDO::PARAM_STR);
    $prepare->bindValue(3, $newOrder[$i], PDO::PARAM_STR);
    $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
    $prepare->execute();
    }
  }

          // $query = "SELECT attendee_person_id FROM TestA_3_order_of_presentation WHERE date = '$date' AND time = (SELECT MAX(time) FROM TestA_3_order_of_presentation WHERE date = '$date');";
          // $st    = $dbh->query("$query");
          // foreach ($st as $row) {
          //     $newOrder[] = $row['attendee_person_id'];
          // }

      if ($_POST['add']) { // 追加が押されたら．

        if ($_POST['my_id'] == null) {
          exit(名前が選択されていません．);
        }
          $addname_id = $_POST['my_id'];

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
          $sql = <<< EOM
            SELECT attendee_person_id
            FROM {$tbname_3}
            WHERE date = ?
            AND   time = (
              SELECT MAX(time)
              FROM {$tbname_3}
              WHERE date = ?
              )
            ;
EOM;
            $prepare = $dbh->prepare($sql);
            $prepare->bindValue(1, $date, PDO::PARAM_STR);
            $prepare->bindValue(2, $date, PDO::PARAM_STR);
            $prepare->execute();

            foreach ($prepare as $row) {
                $newOrder[] = $row['attendee_person_id'];
            }

            // 最後に一つ，追加する．
            $newOrder[] = $addname_id;

            for ($i = 0; $i < count($newOrder); ++$i) {
                $j   = $i + 1;

                $sql_insert_2 = <<< EOM
                  INSERT INTO {$tbname_3} (date, time, attendee_person_id, order_of_presen)
                  VALUES (?, ?, ?, ?) ;
EOM;
                $prepare = $dbh->prepare($sql_insert_2);
                $prepare->bindValue(1, $date, PDO::PARAM_STR);
                $prepare->bindValue(2, $time, PDO::PARAM_STR);
                $prepare->bindValue(3, $newOrder[$i], PDO::PARAM_STR);
                $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
                $prepare->execute();

            }
          }

          // $query = "SELECT order_of_presen FROM TestA_3_order_of_presen WHERE date = '$date' ORDER BY order_of_presen desc LIMIT 1";
          //

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

          $sql_search_attendee = <<< EOM
            SELECT studentname, person_id
            FROM  {$tbname_2}
            LEFT JOIN {$tbname_3}
            ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
            AND {$tbname_3}.date = ?
            AND time = (
              SELECT MAX(time)
              FROM {$tbname_3}
              WHERE date = ? )
            WHERE {$tbname_3}.attendee_person_id IS NULL
            AND fiscal_year = ? ;
EOM;
          $prepare_attendee = $dbh->prepare($sql_search_attendee);
          $prepare_attendee->bindValue(1, $date, PDO::PARAM_STR);
          $prepare_attendee->bindValue(2, $date, PDO::PARAM_STR);
          $prepare_attendee->bindValue(3, $fiscalyear, PDO::PARAM_STR);
          $prepare_attendee->execute();

        // where TestA_3_order_of_presen.date = '$date'
        // where TestA_3_order_of_presen.attendee_person_id is null

      // これで済むはずなのに……　<?php include 'current_exOrder.php';
    $sql = <<< EOM
      SELECT studentname
      FROM  {$tbname_2}
      LEFT JOIN {$tbname_3}
      ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
      WHERE {$tbname_3}.date = ?
      AND   time = (
        SELECT MAX(time)
        FROM {$tbname_3}
        WHERE date = ?)
      ORDER BY {$tbname_3}.order_of_presen;
EOM;
      $prepare_presen_order = $dbh->prepare($sql);
      $prepare_presen_order->bindValue(1, $date, PDO::PARAM_STR);
      $prepare_presen_order->bindValue(2, $date, PDO::PARAM_STR);
      $prepare_presen_order->execute();

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
<title>発表順の編集</title>
</head>
<body>
<h2>＜編集＞</h2>

<!-- addname form -->
<h3>[名前を追加する]</h3>
<form method="post" action="request_exOrder.php">

<?php foreach ($prepare_attendee as $row): ?>
<?php $name = $row['studentname']; ?>
<?php $id = $row['person_id']; ?>
  <label>
    <input type='radio' name='my_id' value=<?=h($id)?> checked /><?=h($name)?>
    <br /><br />
  </label>
<?php endforeach; ?>


<input type="submit" name="add" value="決定" >
</form>

<!-- delete form -->
<br><h3>[名前を削除する]（※一名ずつ）</h3>
<form method="post" action="request_exOrder.php">
<input type="submit" name="delete" value="DELETE" >
</form>


<br>
<h2>＜現在の設定＞</h2>

<!-- これで済むはずなのに…… include 'current_exOrder.php'; -->
<table border='1' cellpadding='5' style='background:#F0F8FF'>
<?php $i = 1; ?>
<?php foreach ($prepare_presen_order as $row): ?>
  <tr>
    <td><?=h($i) ?></td>
    <td><?=h($row['studentname'])?></td>
  </tr>
<?php $i = $i + 1; ?>
<?php endforeach; ?>
</table>

<h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
<p><a href= index.html > TOP </a></p><br><br>
</body>
</html>
