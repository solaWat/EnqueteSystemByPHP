<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {
  /**
   * まず，本プログラムで使用するDBを作成する．
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
   * DBに，4つのTABLEを作成する．
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
   * 研究室所属メンバーを
   * プレゼン用とファシグラ用に
   * それぞれDBから取ってくる．
   */
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
   *  POST（ランダムで決める）が降ってきた際の処理．
   *  プレゼン欄で選択された人のシャッフルして，
   *  プレゼン順を提案する．
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

  /**
   *  POST（ランダムで決める）が降ってきた際の処理．
   *  ファシグラ欄で選択された人のシャッフルして，
   *  ファシグラ順を提案する．
   */
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
   * 発表順（プレゼン順）を参考にして，ファシグラ順を提案する．
   * 現在の設定は，発表者の2つ後の発表者が，ファシグラを担当する．
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

    // 発表順を参照して，所属メンバーから本日のゼミ参加者情報を引っ張ってくる．
    // 発表順は，その日かつ最新の時間，の物を取ってくる．
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

    /**
     * 入ってきた配列の順番を入れ替えるための関数．
     * i 番目の値に，i+2 番目の値を入れるようになっている．
     * @param  array $array 入力配列
     * @return array $person_fg 出力配列
     */
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
   * 現在の順番をプレゼンとファシグラ，
   * それぞれDBから吸い出す．
   */
  // これで済むはずなのに……　<?php include 'current_exOrder.php';
  // プレゼン用
  $sql = <<< EOM
    SELECT studentname
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
     AND time = (
       SELECT MAX(time)
       FROM {$tbname_3}
       WHERE date = ? )
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
	<title>発表順をシャッフルで指定</title>
  <!-- BootstrapのCSS読み込み -->
  <link href="bootstrap-4.3.1-dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- BootstrapのJS読み込み -->
  <script src="bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
  <!-- iconの読み込み　外部サイト：「Font Awesome」-->
  <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js" integrity="sha384-0pzryjIRos8mFBWMzSSZApWtPl/5++eIfzYmTgBBmXYdhvxPc+XcFEk+zJwDgWbP" crossorigin="anonymous"></script>
  <!--<div style="background-color: #cff;">-->
</head>
<body>
  <?php include ('header_general.php'); ?>
  
  <div class="row">
    <div class="col text-left">
      <h2>順番をシャッフルで指定する</h2>
    </div>
  </div>
  <div class="row">
    <div class="col">
      
    </div>
    <div class="col-5 text-center btn shadow">
      <h3>所属者一覧</h3>
      <div class="card-deck">
        <div class="card">
          <div class="card-header">プレゼンテーション</div>
          <div class="card-body mx-auto">
            <form method="post" action="exMember.php">
              <?php foreach ($prepare_memberinfo as $row): ?>
                <?php $name   = $row['studentname'];?>
                <?php $id = $row['person_id'];?>
                <div class="form-check text-left pl-5">
                  <label class="form-check-label m-1">
                    <h5>
                      <input type='checkbox' class="form-check-input" name='cn_pr[]' value='<?=h($id)?>'><?=h($name)?>
                    </h5>
                    
                  </label>
                </div>
              <?php endforeach; ?>
              <button type="button" class="btn bg-info text-white mt-3" data-toggle="modal" data-target="#myModal_pr">
                <i class="fas fa-random"></i> シャッフルで順番を決める
              </button>
              
              <!-- The Modal -->
              <div class="modal" id="myModal_pr">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-warning">
                      <h4 class="modal-title text-white">注意</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-left">
                      <p>・未選択で「続行」した場合でも，現在の"プレゼン"の順番は削除されます．</p>
                      <p>・削除されたデータは元に戻りません．</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">中止</button>
                      <button type="submit" class="btn btn-primary" name="sort_pr" value="保存">続行</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- The Modal -->
       
            </form>
          </div> 
        </div>
        <div class="card">
          <div class="card-header">ファシグラ</div>
          <div class="card-body mx-auto">
            <form method="post" action="exMember.php">
              <?php foreach ($prepare_memberinfo_fg as $row): ?>
                <?php $name   = $row['studentname'];?>
                <?php $id = $row['person_id'];?>
                <div class="form-check text-left pl-5">
                  <label class="form-check-label m-1">
                    <h5>
                      <input type='checkbox' class="form-check-input" name='cn_fg[]' value='<?=h($id)?>'><?=h($name)?>
                    </h5>
                  </label>
                </div>
              <?php endforeach; ?>
              <button type="button" class="btn bg-info text-white mt-3" data-toggle="modal" data-target="#myModal_fg">
                <i class="fas fa-random"></i> シャッフルで順番を決める
              </button>
              
              <!-- The Modal -->
              <div class="modal" id="myModal_fg">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-warning">
                      <h4 class="modal-title text-white">注意</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-left">
                      <p>・未選択で「続行」した場合でも，現在の"ファシグラ"の順番は削除されます．</p>
                      <p>・削除されたデータは元に戻りません．</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">中止</button>
                      <button type="submit" class="btn btn-primary" name="sort_fg" value="保存">続行</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- The Modal -->
              
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
              <?php foreach ($prepare as $row): ?>
                <tr>
                  <td><?=h($i) ?></td>
                  <td><?=h($row['studentname'])?></td>
                </tr>
                <?php $i = $i + 1; ?>
              <?php endforeach; ?>
            </table>
          </div>
        </div>
        <div class="card border-secondary">
          <div class="card-header">ファシグラ</div>
          <div class="card-body mx-auto">
            <table class="table table-bordered text-center" style='background:#F5F5F5'>
              <?php $i = 1; ?>
              <?php foreach ($prepare_fg as $row): ?>
                <tr>
                  <td><?=h($i) ?></td>
                  <td><?=h($row['studentname'])?></td>
                </tr>
                <?php $i = $i + 1; ?>
              <?php endforeach; ?>
            </table>
          </div> 
        </div>
      </div>
      <form method="post" action="exMember.php">
        <button type="submit" class="btn bg-primary text-white mt-3" name="arrange" value="発表順の2つ先の名前を，ファシグラ担当にする" >
          <i class="fas fa-tools"></i> 発表順の2つ先の名前を，ファシグラ担当にする
        </button>
      </form>
    </div>
    <div class="col">
      
    </div>
  </div>
  
  <div class="row mt-3">
    <div class="col text-left">
      <h2>順番を任意に指定する</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-1">
      
    </div>
    <div class="col">
			<div class="card-deck">
			    <a href=request_exOrder.php class="btn card border-danger text-danger shadow-sm">
					<div class="card-body">
						<div class="card-header"><i class="fas fa-user"></i> 代表者</div>
						<p></p>
						<p class="card-text"><i class="far fa-hand-point-up"></i> 順番を手入力で編集</p>
					</div>
				</a>
			</div>
		</div>
		<div class="col-7">
      
    </div>
  </div>
  <?php include ('footer_general.php'); ?>
</body>
</html
