<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {

  /**
   * 後の処理で使い回す $dbh を作る．
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

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * プレゼン用
   */
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
      $id_order[]                 = $row['person_id'];
  }
  // PRでもFGでもどちらでもいいが，発表の回数を保持しておく
  $attendee_person_number = count($attendee_studentname);

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * ファシグラ用
   */
  $sql_fg = <<< EOM
    SELECT studentname, person_id
    FROM {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    WHERE {$tbname_4}.date = ?
     AND time =
     ( SELECT MAX(time)
       FROM {$tbname_4}
       WHERE date = ? )
    ORDER BY {$tbname_4}.order_of_fg;
EOM;
  $prepare_fg = $dbh->prepare($sql_fg);
  $prepare_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_fg->execute();

  foreach ($prepare_fg as $row) {
      $attendee_studentname_fg[] = $row['studentname'];
      $id_order_fg[]                 = $row['person_id'];
  }

  /**
   * データベースにアクセスして，
   * 投票データを抽出し，
   * 集計を行う．
   */
  // P票の集計
  for ($i = 0; $i < count($id_order); ++$i) {
    $one_person_id = $id_order[$i];
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
  for ($i = 0; $i < count($id_order_fg); ++$i) {
    $one_person_id = $id_order_fg[$i];
    $sql_fg = <<< EOM
      SELECT
        COUNT(rank = ? or null) AS rank1_num,
        COUNT(rank = ? or null) AS rank2_num,
        COUNT(rank = ? or null) AS rank3_num
      FROM {$tbname_1}
      WHERE date = ?
       AND types_of_votes = ?
       AND voted_person_id = ? ;
EOM;
    $prepare_fg = $dbh->prepare($sql_fg);
    $prepare_fg->bindValue(1, '1', PDO::PARAM_STR);
    $prepare_fg->bindValue(2, '2', PDO::PARAM_STR);
    $prepare_fg->bindValue(3, '3', PDO::PARAM_STR);
    $prepare_fg->bindValue(4, $date, PDO::PARAM_STR);
    $prepare_fg->bindValue(5, 'FG', PDO::PARAM_STR);
    $prepare_fg->bindValue(6, $one_person_id, PDO::PARAM_STR);
    $prepare_fg->execute();
    foreach ($prepare_fg as $row) {
        $sum_voted_FG[] =
        ($row['rank1_num'] * 3) +
        ($row['rank2_num'] * 2) +
        ($row['rank3_num'] * 1)   ;
    }
  }

  /**
   * 投票が終わった人数の集計
   * vote テーブルの投票者のユニーク数を数える．
   */
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

  /**
   * POST（リセット）が押された際の処理
   * その日の vote テーブルのデータを全て削除．
   */
  if ($_POST['delete_result']) {
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
  <!-- UTF-8 or Shift_JIS or EUC-JP -->
  <!-- 11秒ごとにページを自動更新 -->
  <META HTTP-EQUIV="Refresh" CONTENT="11">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>集計結果の表示</title>
  <!-- BootstrapのCSS読み込み -->
  <link href="bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- BootstrapのJS読み込み -->
  <script src="bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
  <!-- iconの読み込み　外部サイト：「Font Awesome」-->
  <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js" integrity="sha384-0pzryjIRos8mFBWMzSSZApWtPl/5++eIfzYmTgBBmXYdhvxPc+XcFEk+zJwDgWbP" crossorigin="anonymous"></script>
</head>
<body>
  
  <?php include ('header_general.php'); ?>
  
  <div class="media border p-3">
    <!--<img src="rest_nobita.jpg" alt="rest_nobita" class="mr-3 mt-3" style="width:120px;">-->
    <div class="media-body text-center">
      <h5><i class="fas fa-mug-hot"></i> お疲れ様でした．投票結果は自動的に更新されます</h5>
    </div>
  </div>
  
  <div class="row mt-3">
    <div class="col">
      
    </div>
    <div class="col-5 text-center btn shadow">
      <h3>投票状況</h3>
      <div class="card-deck">
        <div class="card">
          <div class="progress">
            <div class="progress-bar progress-bar-striped" style="width:<?=h(100 * $finish_vote_num / $attendee_person_number)?>%">
              <?=h($finish_vote_num)?> 人
            </div>
            <div class="progress-bar bg-secondary" style="width:<?=h(100 - 100 * $finish_vote_num / $attendee_person_number)?>%">
              <?=h($attendee_person_number - $finish_vote_num)?> 人
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      
    </div>
  </div>
  
  <div class="row mt-5">
    <!--<div class="col-1">-->
      
    <!--</div>-->
    <div class="col text-center">
      <div class="col text-center p-3">
        <h3>投票結果（ <?=h($date)?> )</h3>
        <div class="card-deck">
          <div class="card border border-info shadow">
            <div class="card-header">プレゼンテーション</div>
            <div class="card-body mx-auto">
              <table class="table table-bordered" style='background:#F0F8FF'>
                <?php for ($i = 0; $i < count($id_order); ++$i) { ?>
                  <tr>
                    <td class="text-center" style="background:white">
                      <h4>
                        <?=h($attendee_studentname[$i])?>
                      </h4>
                    </td>
                    <td>
                      <table class="table table-sm table-borderless my-auto">
                        <tr>
                          <?php $w = 1 + $sum_voted_P[$i] * 20; ?>
                          <td width=<?= $w ?> class="shadow" bgcolor='green'>
                          </td>
                          <td>
                            <?=h($sum_voted_P[$i])?> 票
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>
          </div>
      
          <div class="card border border-secondary shadow">
            <div class="card-header">ファシグラ</div>
            <div class="card-body mx-auto">
              <table class="table table-bordered" style="background:#F5F5F5">
                <?php for ($i = 0; $i < count($id_order_fg); ++$i) { ?>
                  <tr>
                    <td class="text-center" style="background:white">
                      <h4>
                        <?=h($attendee_studentname_fg[$i])?>
                      </h4>
                    </td>
                    <td>
                      <table class="table table-sm table-borderless my-auto">
                        <tr>
                          <?php $w = 1 + $sum_voted_FG[$i] * 20; ?>
                          <td width=<?= $w ?> class="shadow" bgcolor='green'>
                          </td>
                          <td>
                            <?=h($sum_voted_FG[$i])?> 票
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--<div class="col-1">-->
      
    <!--</div>-->
  </div>
  
  <div class="row mt-5">
    <div class="col">
      
    </div>
    <div class="col-3">
      <form method="post" action="resultVis.php">
        <!--<input type="submit" name="delete_result" value="※押すな※　本日の投票データを全て削除　※">-->
        <button type="button" class="btn bg-danger text-white mt-3" data-toggle="modal" data-target="#Modal_delete_resVote">
          ※押すな※　本日の投票データを全て削除　※
        </button>
      
      
        <!-- The Modal -->
        <div class="modal" id="Modal_delete_resVote">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger">
                <h4 class="modal-title text-white">警告</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body text-left">
                <p>本日の投票データが全て削除されます．それでもよろしければ「続行」を押してください．</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">中止</button>
                <button type="submit" class="btn btn-danger" name="delete_result" value="※押すな※　本日の投票データを全て削除　※">続行</button>
              </div>
            </div>
          </div>
        </div>
        <!-- The Modal -->
      
      </form>
      
    </div>
  </div>
  
  <?php include ('footer_general.php'); ?>

</body>
</html>
