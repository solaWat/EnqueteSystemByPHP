<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>発表順の編集</title>
</head>
<body>
<?php
if ($_POST['delete']) { // デリートが押されたら．
  date_default_timezone_set('Asia/Tokyo');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    try {
        $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
    $dbh->query('USE enquete_main');

        $query = "SELECT attendee_person_id FROM TestA_3_order_of_presentation WHERE date = '$date' AND time = (SELECT MAX(time) FROM TestA_3_order_of_presentation WHERE date = '$date');";
        $st    = $dbh->query("$query");
        foreach ($st as $row) {
            $newOrder[] = $row['attendee_person_id'];
        }

        for ($i = 0; $i < count($newOrder) - 1; ++$i) {
            $j   = $i + 1;
            $sql = "INSERT INTO TestA_3_order_of_presentation (date, time, attendee_person_id, order_of_presen) VALUES ('$date', '$time', '$newOrder[$i]', '$j') ";
            $st  = $dbh->prepare($sql);
            $st->execute();
        }
    } catch (PDOException $e) {
        echo 'エラー!: '.$e->getMessage().'<br/>';
        die();
    }

  // $sql = "DELETE FROM TestA_3_order_of_presen where date = '$date' order by order_of_presen desc limit 1";
  // $st = $dbh->prepare($sql);
  // $st->execute();
}

if ($_POST['add']) { // 追加が押されたら．

  if ($_POST['my_id'] == null) { // ボタンはcheckedされてるので，出番ないかも．
    exit(名前が選択されていません．);
  }

    $addname_id = $_POST['my_id'];

    date_default_timezone_set('Asia/Tokyo');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    try {
        $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
    $dbh->query('USE enquete_main');

        $query = "SELECT attendee_person_id FROM TestA_3_order_of_presentation WHERE date = '$date' AND time = (SELECT MAX(time) FROM TestA_3_order_of_presentation WHERE date = '$date');";
    // $query = "SELECT order_of_presen FROM TestA_3_order_of_presen WHERE date = '$date' ORDER BY order_of_presen desc LIMIT 1";
    $st = $dbh->query("$query");

        foreach ($st as $row) {
            $newOrder[] = $row['attendee_person_id'];
        }

        $newOrder[] = $addname_id;

    // $food = $_POST['cn'];
    // srand(time()); //乱数列初期化．冗長の可能性あり．
    // shuffle($food); //　出席者をランダムソートにかけ，発表順を決める．

    for ($i = 0; $i < count($newOrder); ++$i) {
        $j   = $i + 1;
        $sql = "INSERT INTO TestA_3_order_of_presentation (date, time, attendee_person_id, order_of_presen) VALUES ('$date', '$time', '$newOrder[$i]', '$j') ";

        $st = $dbh->prepare($sql);
        $st->execute();
    }
    } catch (PDOException $e) {
        echo 'エラー!: '.$e->getMessage().'<br/>';
        die();
    }
}
?>


<h2>＜編集＞</h2>

<!-- addname form -->
<h3>[名前を追加する]</h3>
<form method="post" action="request_exOrder.php">
<?php


date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');

try {
    $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query('USE enquete_main');

    $query = <<< EOM
    SELECT studentname, person_id
    FROM  TestA_2_lab_member_info
    LEFT JOIN TestA_3_order_of_presentation
    ON TestA_2_lab_member_info.person_id = TestA_3_order_of_presentation.attendee_person_id
    AND TestA_3_order_of_presentation.date = '$date'
    AND time = (SELECT MAX(time) FROM TestA_3_order_of_presentation WHERE date = '$date')
    WHERE TestA_3_order_of_presentation.attendee_person_id IS NULL
    AND fiscal_year = '2016' ;
EOM;
    $st = $dbh->query("$query");

  // where TestA_3_order_of_presen.date = '$date'
  // where TestA_3_order_of_presen.attendee_person_id is null

  foreach ($st as $row) {
      $name = $row['studentname'];
      $id   = $row['person_id'];
      echo "<label><input type='radio' name='my_id' value='$id' checked>{$name}<br><br></label>";
  }
} catch (PDOException $e) {
    echo 'エラー!: '.$e->getMessage().'<br/>';
    die();
}

?>
<input type="submit" name="add" value="決定" >
</form>

<!-- delete form -->
<br><h3>[名前を削除する]（※一名ずつ）</h3>
<form method="post" action="request_exOrder.php">
<input type="submit" name="delete" value="DELETE" >
</form>


<br>
<h2>＜現在の設定＞</h2>
<?php include 'current_exOrder.php'; ?>

<h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
<p><a href= index.html > TOP </a></p><br><br>
</body>
</html>
