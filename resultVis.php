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
    SELECT studentname, person_id
    FROM {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
     AND time =
     ( SELECT MAX(time)
       FROM {$tbname_3}
       WHERE date = ? )
    ORDER BY {$tbname_3}.order_of_presen;
EOM;

  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $date, PDO::PARAM_STR);
  $prepare->bindValue(2, $date, PDO::PARAM_STR);
  $prepare->execute();

  foreach ($prepare as $row) {
      $attendee_studentname[] = $row['studentname'];
      $hoge[]                 = $row['person_id'];
  }

  $attendee_person_number = count($attendee_studentname);

  // P票の集計
  for ($i = 0; $i < count($hoge); ++$i) {
    $one_person_id = $hoge[$i];
    $sql = <<< EOM
      SELECT
        COUNT(rank = ? or null) AS rank1_num,
        COUNT(rank = ? or null) AS rank2_num,
        COUNT(rank = ? or null) AS rank3_num
      FROM {$tbname_1}
      WHERE date = ?
       AND types_of_votes = ?
       AND voted_person_id = ? ;
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, '1', PDO::PARAM_STR);
    $prepare->bindValue(2, '2', PDO::PARAM_STR);
    $prepare->bindValue(3, '3', PDO::PARAM_STR);
    $prepare->bindValue(4, $date, PDO::PARAM_STR);
    $prepare->bindValue(5, 'P', PDO::PARAM_STR);
    $prepare->bindValue(6, $one_person_id, PDO::PARAM_STR);
    $prepare->execute();
    foreach ($prepare as $row) {
        $sum_voted_P[] =
        ($row['rank1_num'] * 3) +
        ($row['rank2_num'] * 2) +
        ($row['rank3_num'] * 1)   ;
    }
  }

  // FG票の集計
  for ($i = 0; $i < count($hoge); ++$i) {
    $one_person_id = $hoge[$i];
    $sql = <<< EOM
      SELECT
        COUNT(rank = ? or null) AS rank1_num,
        COUNT(rank = ? or null) AS rank2_num,
        COUNT(rank = ? or null) AS rank3_num
      FROM {$tbname_1}
      WHERE date = ?
       AND types_of_votes = ?
       AND voted_person_id = ? ;
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, '1', PDO::PARAM_STR);
    $prepare->bindValue(2, '2', PDO::PARAM_STR);
    $prepare->bindValue(3, '3', PDO::PARAM_STR);
    $prepare->bindValue(4, $date, PDO::PARAM_STR);
    $prepare->bindValue(5, 'FG', PDO::PARAM_STR);
    $prepare->bindValue(6, $one_person_id, PDO::PARAM_STR);
    $prepare->execute();
    foreach ($prepare as $row) {
        $sum_voted_FG[] =
        ($row['rank1_num'] * 3) +
        ($row['rank2_num'] * 2) +
        ($row['rank3_num'] * 1)   ;
    }
  }

  // 投票が終わった人の集計
  $sql = <<< EOM
    SELECT DISTINCT voter_person_id
    FROM {$tbname_1}
    WHERE date = ?
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $date, PDO::PARAM_STR);
  $prepare->execute();
  foreach ($prepare as $row) {
      $forSum[]        = $row['voter_person_id'];
      $finish_vote_num = count($forSum);
  }

  // リセットボタン用
  if ($_POST['submit2']) {
    $sql = <<< EOM
      DELETE
      FROM {$tbname_1}
      where date = ?
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->execute();
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
<!-- 11秒ごとにページを自動更新 -->
<META HTTP-EQUIV="Refresh" CONTENT="11">
<title>集計結果の表示</title>
</head>
<body>
お疲れ様でした．投票結果は自動的に更新されます．<br><br>
<!-- 休憩してて良いことを示す画像 -->
<img src="rest_nobita.jpg"></img><br><br><br>

<p>
  現在，『 <?=h($finish_vote_num)?> 人 』の投票が終わっています.
</p>
<p>
  （発表した人の数は <?=h($attendee_person_number)?> 人です．）
</p>
<br><br>

<!-- 2つのテーブルと並列表示させるための透明テーブル -->
<table>
  <caption>
    投票結果（ <?=h($date)?> )
  </caption>
  <tr>
    <td>
      <table border="1" style="background:#F0F8FF">
        <caption align='left'>　　　　　　プレゼン
          <?php for ($i = 0; $i < count($hoge); ++$i) { ?>
            <tr>
              <td style="background:white">
                <?=h($attendee_studentname[$i])?>
              </td>
              <td>
                <table>
                  <tr>
                    <?php $w = $sum_voted_P[$i] * 10; ?>
                    <td width=<?= $w ?> bgcolor='green'>
                    </td>
                    <td>
                      <?=h($sum_voted_P[$i])?> 票
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          <?php } ?>
        </caption>
      </table>
    </td>

    <td>
      <table border="1" style="background:#F5F5F5">
        <caption>ファシグラ
          <tr>
            <?php for ($i = 0; $i < count($hoge); ++$i) { ?>
            <tr>
              <td>
                <table>
                  <tr>
                    <?php $w = $sum_voted_FG[$i] * 10; ?>
                    <td width=<?= $w ?> bgcolor='green'>
                    </td>
                    <td>
                      <?=h($sum_voted_FG[$i])?> 票
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </tr>
        <?php } ?>

      </table>
    </td>

  </tr>
</table>
<br><br>
<p>
  <font color="brue">「shift」+「command」+「4」で，範囲を指定して，投票結果をスクリーンショットしてください．(mac)</font>
</p><br><br>
<a href= index.html > TOP </a>

<br><br><br><br><br><br><br><br><br><br>

<form method="post" action="resultVis.php">
  <input type="submit" name="submit2" value="※押すな※　本日の投票データを全て削除　※">
</form>
<p>
  <font color="red">管理人のつぶやき「なんか，全体的に殺風景だ……」</font>
</p>

</body>
</html>
