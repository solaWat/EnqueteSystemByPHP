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

    $insert_info_sql = <<< EOM
      INSERT IGNORE INTO `lab_member_info` (fiscal_year, studentname, person_id) VALUES
      (2017, 'Ghita Athalina', '1701g'),
      (2017, '安保　建朗', '1702a'),
      (2017, '森田　和貴', '1703m'),
      (2017, '渡辺　宇', '1704w'),
      (2017, '荒木　香名', '1705a'),
      (2017, '柴沢　弘樹', '1706s'),
      (2017, '有竹　克馬', '1707a'),
      (2017, '小山　祐希', '1708k'),
      (2017, '持田　成一', '1709m'),
      (2017, '吉田　梢', '1710y')
EOM;
    // $dbh->exec($insert_info_sql);
    $prepare = $dbh->prepare($insert_info_sql);
    $prepare->execute();

} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    echo 'エラー!: '.$e->getMessage().'<br/>';
    die();
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>初回INSERT</title>
</head>
<body>
<p>
  lab_member_info への登録に成功しました．
</p>
<p>
  失敗した時のコピペ
   DELETE FROM `lab_member_info` WHERE `fiscal_year` = '2017'
</p>
<a href= index.html > TOP </a>
</body>
</html>
