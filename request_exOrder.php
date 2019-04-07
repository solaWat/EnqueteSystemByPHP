<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {
  /**
   * POST（名前を削除する）が押された際の処理
   * 現在の順番の一番最後にある名前を，
   * 順番から取り除く．その後，新しい順番をDBに登録する．
   * プレゼン用
   */
  if ($_POST['delete']) {
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
      $sql_delete = <<< EOM
        INSERT INTO {$tbname_3} (date, time, attendee_person_id, order_of_presen)
        VALUES (?, ?, ?, ?) ;
EOM;
    $prepare = $dbh->prepare($sql_delete);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $time, PDO::PARAM_STR);
    $prepare->bindValue(3, $newOrder[$i], PDO::PARAM_STR);
    $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
    $prepare->execute();
    }
  }

  /**
   * POST（名前を削除する）が押された際の処理
   * 現在の順番の一番最後にある名前を，
   * 順番から取り除く．その後，新しい順番をDBに登録する．
   * ファシグラ用
   */
  if ($_POST['delete_fg']) {
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
      FROM {$tbname_4}
      WHERE date = ?
      AND   time = (
        SELECT MAX(time)
        FROM {$tbname_4}
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
      $sql_delete_fg = <<< EOM
        INSERT INTO {$tbname_4} (date, time, attendee_person_id, order_of_fg)
        VALUES (?, ?, ?, ?) ;
EOM;
    $prepare = $dbh->prepare($sql_delete_fg);
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
  /**
   * POST（名前を追加する）が押された際の処理
   * 選択された1名を現在の順番の最後に付け加える．
   * その後，新しい順番をDBに登録する．
   * プレゼン用
   */
  if ($_POST['add']) {
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
        );
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $date, PDO::PARAM_STR);
    $prepare->execute();

    foreach ($prepare as $row) {
        $newOrder[] = $row['attendee_person_id'];
    }
    // 現在の順番の最後に，一つ追加する．
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

  /**
   * POST（名前を追加する）が押された際の処理
   * 選択された1名を現在の順番の最後に付け加える．
   * その後，新しい順番をDBに登録する．
   * ファシグラ用
   */
  if ($_POST['add_fg']) {
    if ($_POST['my_id_fg'] == null) {
      exit(名前が選択されていません．);
    }
    $addname_id = $_POST['my_id_fg'];
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
      FROM {$tbname_4}
      WHERE date = ?
      AND   time = (
        SELECT MAX(time)
        FROM {$tbname_4}
        WHERE date = ?
        );
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $date, PDO::PARAM_STR);
    $prepare->execute();

    foreach ($prepare as $row) {
        $newOrder[] = $row['attendee_person_id'];
    }
    // 現在の順番の最後に，一つ追加する．
    $newOrder[] = $addname_id;

    for ($i = 0; $i < count($newOrder); ++$i) {
      $j   = $i + 1;
      $sql_insert_fg = <<< EOM
        INSERT INTO {$tbname_4} (date, time, attendee_person_id, order_of_fg)
        VALUES (?, ?, ?, ?) ;
EOM;
      $prepare = $dbh->prepare($sql_insert_fg);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $newOrder[$i], PDO::PARAM_STR);
      $prepare->bindValue(4, (int)$j, PDO::PARAM_INT);
      $prepare->execute();
    }
  }

          // $query = "SELECT order_of_presen FROM TestA_3_order_of_presen WHERE date = '$date' ORDER BY order_of_presen desc LIMIT 1";
          //
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
   * あらかじめ登録しておいた
   * 研究室現所属者の名簿データベースから，
   * 本日の参加者として登録してる人の名前を引いて，
   * 差分をを保持する．
   * プレゼン用
   */
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

  /**
   * あらかじめ登録しておいた
   * 研究室現所属者の名簿データベースから，
   * 本日の参加者として登録してる人の名前を引いて，
   * 差分をを保持する．
   * ファシグラ用
   */
  $sql_search_attendee_fg = <<< EOM
    SELECT studentname, person_id
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    AND {$tbname_4}.date = ?
    AND time = (
      SELECT MAX(time)
      FROM {$tbname_4}
      WHERE date = ? )
    WHERE {$tbname_4}.attendee_person_id IS NULL
    AND fiscal_year = ? ;
EOM;
  $prepare_attendee_fg = $dbh->prepare($sql_search_attendee_fg);
  $prepare_attendee_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_attendee_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_attendee_fg->bindValue(3, $fiscalyear, PDO::PARAM_STR);
  $prepare_attendee_fg->execute();

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * プレゼン用
   */
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
  $prepare_order_pr = $dbh->prepare($sql);
  $prepare_order_pr->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_pr->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_pr->execute();

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
  $prepare_order_fg = $dbh->prepare($sql_fg);
  $prepare_order_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_fg->execute();

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
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>発表順の編集</title>
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
  
  <div class="row">
    <div class="col text-left">
      <h2>順番を任意に指定する</h2>
    </div>
  </div>
  
  <div class="row">
    <div class="col">
      
    </div>
    <div class="col-5 text-center btn shadow">
      <h3>未選択の所属者一覧</h3>
      <div class="card-deck">
        <div class="card">
          <div class="card-header">プレゼンテーション</div>
          <div class="card-body mx-auto">
            <form method="post" action="request_exOrder.php">
              <?php foreach ($prepare_attendee as $row): ?>
                <?php $name = $row['studentname'];?>
                <?php $id = $row['person_id'];?>
                <div class="form-check text-left">
                  <label class="form-check-label m-1">
                    <h5>
                      <input type='radio' class="form-check-input" name='my_id' value=<?=h($id)?> checked /><?=h($name)?>
                    </h5>
                  </label>
                </div>
              <?php endforeach; ?>
              <button type="submit" class="btn bg-primary text-white mt-3" name="add" value="追加する">
                <i class="fas fa-user-plus"></i> 一番下に追加
              </button>
            </form>
          </div> 
        </div>
        <div class="card">
          <div class="card-header">ファシグラ</div>
          <div class="card-body mx-auto">
            <form method="post" action="request_exOrder.php">
              <?php foreach ($prepare_attendee_fg as $row): ?>
                <?php $name = $row['studentname']; ?>
                <?php $id = $row['person_id']; ?>
                <div class="form-check text-left">
                  <label class="form-check-label m-1">
                    <h5>
                      <input type='radio' class="form-check-input" name='my_id_fg' value=<?=h($id)?> checked /><?=h($name)?>
                    </h5>
                  </label>
                </div>
              <?php endforeach; ?>
              <button type="submit" class="btn bg-primary text-white mt-3" name="add_fg" value="追加する">
                <i class="fas fa-user-plus"></i> 一番下に追加
              </button>
            </form>
          </div> 
        </div>
      </div>
    </div>

    <div class="col">
      
    </div>
    
    <div class="col-5 text-center btn">
      <h3>現在の発表順</h3>
      <div class="card-deck">
        <div class="card border-info">
          <div class="card-header">プレゼンテーション</div>
          <div class="card-body mx-auto">
            <table class="table table-bordered text-center" style='background:#F0F8FF'>
              <?php $i = 1; ?>
              <?php foreach ($prepare_order_pr as $row): ?>
                <tr>
                  <td><?=h($i) ?></td>
                  <td><?=h($row['studentname'])?></td>
                </tr>
                <?php $i = $i + 1; ?>
              <?php endforeach; ?>
            </table>
            <form method="post" action="request_exOrder.php">
              <button type="submit" class="btn bg-danger text-white mt-3" name="delete" value=" 削除する " >
                <i class="fas fa-user-minus"></i> 一番下を削除
              </button>
            </form>
          </div>
        </div>
        <div class="card border-secondary">
          <div class="card-header">ファシグラ</div>
          <div class="card-body mx-auto">
            <table class="table table-bordered text-center" style='background:#F5F5F5'>
              <?php $i = 1; ?>
              <?php foreach ($prepare_order_fg as $row): ?>
                <tr>
                  <td><?=h($i) ?></td>
                  <td><?=h($row['studentname'])?></td>
                </tr>
                <?php $i = $i + 1; ?>
              <?php endforeach; ?>
            </table>
            <form method="post" action="request_exOrder.php">
              <button type="submit" class="btn bg-danger text-white mt-3" name="delete_fg" value=" 削除する " >
                <i class="fas fa-user-minus"></i> 一番下を削除
              </button>
            </form>
          </div> 
        </div>
      </div>
    </div>
    <div class="col">
      
    </div>
  </div>
  
  <!--<h1>TODO:ボタンの連打対策</h1>-->
  
  <?php include ('footer_general.php'); ?>
</body>
</html>
