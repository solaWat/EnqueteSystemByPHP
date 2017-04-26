<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

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

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * プレゼン用
   */
  $sql = <<< EOM
    SELECT studentname
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
     AND time = (
       SELECT MAX(time)
       FROM {$tbname_3}
       WHERE date = ?)
    ORDER BY {$tbname_3}.order_of_presen;
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $date, PDO::PARAM_STR);
  $prepare->bindValue(2, $date, PDO::PARAM_STR);
  $prepare->execute();

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * ファシグラ用
   * （プレゼン用との違いは，基本的に tbnameだけ．）
   */
  $sql_fg = <<< EOM
    SELECT studentname
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    WHERE {$tbname_4}.date = ?
     AND time = (
       SELECT MAX(time)
       FROM {$tbname_4}
       WHERE date = ? )
    ORDER BY {$tbname_4}.order_of_fg;
EOM;
  $prepare_fg = $dbh->prepare($sql_fg);
  $prepare_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_fg->execute();

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
  <table>
    <tr>
      <td>
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
        <table border='1' cellpadding='5' style='background:#F5F5F5'>
          <?php $i = 1; ?>
          <?php foreach ($prepare_fg as $row): ?>
            <tr>
              <!-- <td>→</td> -->
              <td><?=h($row['studentname'])?></td>
            </tr>
            <?php $i = $i + 1; ?>
          <?php endforeach; ?>
        </table>
      </td>
    </tr>
  </table>

<br><br><br><br>
</body>
</html>
