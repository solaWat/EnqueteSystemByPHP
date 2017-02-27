<?php
$dbname = 'enquete_main_2';//各々の環境で変わります．
$dsn = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8';//各々の環境で変わります．
$user = 'root';//各々の環境で変わります．
$password = 'root';//各々の環境で変わります．

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
// mysql:host=127.0.0.1;dbname=enquete_main;charset=utf8
try {
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
    // $dbh->query('USE enquete_main');
$sql = <<< EOM
    SELECT studentname
    FROM  test_lab_member_info
    LEFT JOIN test_order_of_presentation
    ON test_lab_member_info.person_id = test_order_of_presentation.attendee_person_id
    WHERE test_order_of_presentation.date = ?
     AND time = (SELECT MAX(time) FROM test_order_of_presentation WHERE date = ?)
    ORDER BY test_order_of_presentation.order_of_presen;
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $date, PDO::PARAM_STR);

    $prepare->execute();
    //$prepare->fetchAll();

} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    echo 'エラー!: '.$e->getMessage().'<br/>';
    die();
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>現在の出席者と発表順</title>
</head>
<body>
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

<br><br><br><br>
</body>
</html>
