<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {
  /**
   * セッション関係の処理．
   * 特筆すべきは session_regenerate_id(); の処理．
   */
  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
  session_start();
  session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．

  // inputName.php」で選ばれた名前を保持．
  if (isset($_POST['my_id'])) {
      $_SESSION['my_id'] = $_POST['my_id'];
  }
  // これがあると，セッションがとってあった場合，exitになってしまう．
  // else {
  //   echo "あなたの名前がわかりません．";
  //   exit;
  // }
  $token = $_SESSION['token'];
  $fromSession = $_SESSION['my_id'];

  /**
   * 後の処理で使い回す $dbh を作る．
   */
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

  /**
   * セッションを読み込んで，idと名前を照合し，保持する．
   */
  $sql = <<< EOM
    SELECT studentname
    FROM {$tbname_2}
    WHERE person_id = ?
    AND   fiscal_year = ?
EOM;
  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $fromSession, PDO::PARAM_STR);
  $prepare->bindValue(2, $fiscalyear, PDO::PARAM_STR);
  $prepare->execute();

  foreach ($prepare as $row) {
      $masters_name = $row['studentname'];
  }

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * プレゼン用
   */
  $sql_vote = <<< EOM
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
  $prepare_order_pr = $dbh->prepare($sql_vote);
  $prepare_order_pr->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_pr->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_pr->execute();

  foreach ($prepare_order_pr as $key => $row) {
    $name_text_PRESEN[$key]      = $row['studentname'];
    $id_text_PRESEN[$key]        = $row['person_id'];
  }

  /**
   * 現在の順番を，DBにアクセスして保持する．
   * ファシグラ用
   */
  $sql_vote_fg = <<< EOM
    SELECT studentname, person_id
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_4}
    ON {$tbname_2}.person_id = {$tbname_4}.attendee_person_id
    WHERE {$tbname_4}.date = ?
    AND   time = (
      SELECT MAX(time)
      FROM {$tbname_4}
      WHERE date = ? )
    ORDER BY {$tbname_4}.order_of_fg;
EOM;
  $prepare_order_fg = $dbh->prepare($sql_vote_fg);
  $prepare_order_fg->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_order_fg->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_order_fg->execute();

  foreach ($prepare_order_fg as $key => $row) {
    $name_text_FG[$key]      = $row['studentname'];
    $id_text_FG[$key]        = $row['person_id'];
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<div style="background: #F0FFF0;">
  <title>メイン投票ページ</title>
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
  
  <div>
    <h4>こんにちは <font class="text-secondary"><big><?=h($masters_name)?></big></font> さん</h4>
  </div>
  

  <!-- ゼミにおいて，投票の際に意識する評価指標の項目を列挙する -->
  <div class="row">
    <div class="col text-left">
      <h5>⚪︎投票の際，以下のポイントを意識してください</h5>
    </div>
  </div>
  
  <div class="row">
    <div class="col-1">
      
    </div>
    <div class="col  text-left text-danger">
      <div id="accordionPresen">
        <div class="card  font-weight-bold border border-0" style="background: #F0FFF0;">
          <a class="card-link btn" data-toggle="collapse" href="#collapsePresen">
            <div class="card-header bg-light text-danger font-weight-bold">
              ＜プレゼンテーション <i class="fas fa-user-graduate"></i>＞
            </div>
          </a>
          <div id="collapsePresen" class="collapse bg-white" data-parent="#accordionPresen">
            <div class="card-body">
              <dd>- 制限時間が守られていたか（過ぎた後，発表を続けていないか）</dd>
              <dd>- 意見・質問に対して，根拠ある返答がはっきりとなされていたか</dd>
              <dd>- 発表内容に対して興味深いと思える点が1つでもあったか</dd>
              <dd>- 発表者の態度を心がけていたか（例．貧乏ゆすり・うつむき・表情）</dd>
              <dd>- 声が大きく，抑揚がついていたか</dd>
              <dd>- 話にリズム・キレ・ストーリー性があったか</dd>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col  text-left text-danger">
      <div id="accordionFG">
        <div class="card  font-weight-bold border border-0" style="background: #F0FFF0;">
          <a class="card-link btn" data-toggle="collapse" href="#collapseFG">
            <div class="card-header bg-light text-danger font-weight-bold">
              ＜ファシとグラ <i class="fas fa-chalkboard-teacher"></i>＞
            </div>
          </a>
          <div id="collapseFG" class="collapse bg-white" data-parent="#accordionFG">
            <div class="card-body">
              <dd>- 意見（「発表」を含む）の整理ができていたか</dd>
              <dd>   （例．発言の補足のための深掘り・見やすいグラフィック（色，字，文，絵）・意見の書き出し）</dd>
              <dd>- 場に対して，討論が発生するような問題（議題）提起ができていたか</dd>
              <dd>- 討論の切れ目が見極められていたか</dd>
              <dd>- （グラフィックにおいて，フレームワークが活用できていたか）</dd>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-1">
      
    </div>
  </div>

  <div class="row mt-5">
    <div class="col text-center">
      <h5>それぞれの表の中から選択し、投票してください．</h5>
    </div>
  </div>

  <form method="post" action="mainVote.php">

    <div class="row">
      <div class="col-2">
        
      </div>
      <div class="col text-center">
        <div class="card-deck">
          <div class="card border border-0" style="background: #F0FFF0;">
            <table class="table table-bordered" style='background:#F0F8FF'>
              <thead>
                <tr>
                  <th>
                    1位
                  </th>
                  <th>
                    2位
                  </th>
                  <th>
                    3位
                  </th>
                  <th>
                    プレゼンテーション <i class="fas fa-user-graduate"></i>
                  </th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php $k = 0;?>
                <?php foreach ($name_text_PRESEN as $row): ?>
                  <?php if (strpos($id_text_PRESEN[$k], $fromSession) !== false) { ?>
                  <tr>
                    <?php for ($h = 1; $h < 3; ++$h) { ?>
                    <td>
                      <input type = 'radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> disabled>
                    </td>
                    <?php } ?>
                    <td>
                      <input type='radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> disabled>
                    </td>
                    <td>
                      <?=h($name_text_PRESEN[$k])?>
                    </td>
                  </tr>
                  <?php } else { ?>
                  <tr class="mx-auto">
                    <?php for ($h = 1; $h < 3; ++$h) { ?>
                    <td>
                      <input type = 'radio' name='cn<?=$h?>' value=<?=$id_text_PRESEN[$k] ?> >
                    </td>
                    <?php } ?>
                    <td>
                      <input type='radio' name='cn<?=$h ?>' value=<?=$id_text_PRESEN[$k] ?> >
                    </td>
                    <td>
                      <?=h($name_text_PRESEN[$k])?>
                    </td>
                  </tr>
                  <?php } ?>
                  <?php $k = $k + 1; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
      
          <div class="card border border-0" style="background: #F0FFF0;">
            <table class="table table-bordered" style="background:#F5F5F5">
              <thead>
                <tr>
                  <th>
                    ファシとグラ <i class="fas fa-chalkboard-teacher"></i>
                  </th>
                  <th>
                    1位
                  </th>
                  <th>
                    2位
                  </th>
                  <th>
                    3位
                  </th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php $k = 0;?>
                <?php foreach ($name_text_FG as $row): ?>
        
                  <?php if (strpos($id_text_FG[$k], $fromSession) !== false) { ?>
                  <tr>
                    <td>
                      <?=h($name_text_FG[$k])?>
                    </td>
                    <?php for ($h = 1; $h < 3; ++$h) { ?>
                    <td>
                      <input type = 'radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> disabled>
                    </td>
                    <?php } ?>
                    <td>
                      <input type='radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> disabled>
                    </td>
                  </tr>
                  <?php } else { ?>
                  <tr>
                    <td>
                      <?=h($name_text_FG[$k])?>
                    </td>
                    <?php for ($h = 1; $h < 3; ++$h) { ?>
                    <td>
                      <input type = 'radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> >
                    </td>
                    <?php } ?>
                    <td>
                      <input type='radio' name='co<?= $h ?>' value=<?= $id_text_FG[$k] ?> >
                    </td>
                  </tr>
                  <?php } ?>
                  <?php $k = $k + 1; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-2">
        
      </div>
    </div>
    
    <div class="row">
      <div class="col text-center">
        <input type="hidden" name="token" value="<?=$token?>">
        <!--<input type="submit" name="submit" value="投票する" >-->
        <button type="submit" class="btn btn-primary" name="submit" value="投票する">投票する</button>
      </div>
    </div>
    
  </form>

<?php
// 投票ボタン
if ($_POST['submit']) {
  
  $isVoteSuccess = true;
  $isVoteTwice = false;

  //ラジオボタン選択のエラー表示用
  if ($_POST['cn1'] == null or $_POST['cn2'] == null or $_POST['cn3'] == null) {
      // $error_msg = "<h4><font color='red'>※プレゼンテーションの全ての順位を埋めてください</font></h4>";
      $error_msg = '<div class="alert alert-danger fade show text-center">
                      <strong>Error!</strong> 「プレゼンテーション  <i class="fas fa-user-graduate"></i>」の表の中で、選択されていない順位があります
                    </div>';
      echo $error_msg;
      // exit; // エラーを検知すると，投票はデータベースに書き込まれず，集計結果も見に行けなくなる．
      $isVoteSuccess = false;
  }
  if ($_POST['co1'] == null or $_POST['co2'] == null or $_POST['co3'] == null) {
      // $error_msg = "<h4><font color='red'>※ファシリテーション＆グラフィックスの全ての順位を埋めてください</font></h4>";
      $error_msg = '<div class="alert alert-danger fade show text-center">
                      <strong>Error!</strong> 「ファシとグラ <i class="fas fa-chalkboard-teacher"></i>」の表の中で、選択されていない順位があります
                    </div>';
      echo $error_msg;
      // exit; //die関数にしてもいいかも．
      $isVoteSuccess = false;
  }
  if ($_POST['cn1'] == $_POST['cn2'] || $_POST['cn2'] == $_POST['cn3'] || $_POST['cn1'] == $_POST['cn3']) {
      // $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（プレゼンテーション）</font></h4>";
      $error_msg = '<div class="alert alert-danger fade show text-center">
                      <strong>Error!</strong> 「プレゼンテーション  <i class="fas fa-user-graduate"></i>」の表の中で、一つの投票先に複数の異なる順位が選択されています
                    </div>';
      echo $error_msg;
      // exit;
      $isVoteSuccess = false;
  }
  if ($_POST['co1'] == $_POST['co2'] || $_POST['co2'] == $_POST['co3'] || $_POST['co1'] == $_POST['co3']) {
      // $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（ファシとグラ）</font></h4>";
      $error_msg = '<div class="alert alert-danger fade show text-center">
                      <strong>Error!</strong> 「ファシとグラ <i class="fas fa-chalkboard-teacher"></i>」の表の中で、一つの投票先に複数の異なる順位が選択されています
                    </div>';
      echo $error_msg;
      // exit;
      $isVoteSuccess = false;
  }

  // セッションを開始する
  if (!isset($_SESSION)) {
      session_start();
  }
  // セッションに入れておいたトークンを取得
  $session_token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
  // POSTの値からトークンを取得
  $token = isset($_POST['token']) ? $_POST['token'] : '';

  // トークンがない場合は不正扱い
  if ($token === '') {
      // echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      // die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
      $error_msg = '<div class="alert alert-warning fade show text-center">
                      <strong>Warning!</strong> （※多重投票が検知されました．初回の投票以外は，集計に反映されません．）
                    </div>';
      echo $error_msg;
      $isVoteTwice = true;
  }
  // セッションに入れたトークンとPOSTされたトークンの比較
  if ($token !== $session_token) {
      // echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      // die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
      $error_msg = '<div class="alert alert-warning fade show text-center">
                      <strong>Warning!</strong> （※多重投票が検知されました．初回の投票以外は，集計に反映されません．）
                    </div>';
      echo $error_msg;
      $isVoteTwice = true;
  }
  
  if ($isVoteSuccess) {
    // セッションに保存しておいたトークンの削除
    unset($_SESSION['token']);
    
    if (!$isVoteTwice) {
      echo '<div class="alert alert-success fade show text-center">
              <strong>Success!</strong> 投票に成功しました．集計結果を見に行きましょう．
            </div>';
    }
    
    echo '<div class="row">
            <div class="col-4">
              
            </div>
            <div class="col">
        			<div class="card-deck">
        		    <a href=resultVis.php class="btn card border-info text-info shadow-sm">
        					<div class="card-body">
        						<div class="card-header"><i class="far fa-eye"></i></div>
        						<p></p>
        						<p class="card-text"><i class="fas fa-poll-h"></i></i> 集計結果を見る</p>
        					</div>
        				</a>
        			</div>
        		</div>
        		<div class="col-4">
              
            </div>
          </div>';
  }
  
  

  try {
    $sql = <<< EOM
      DELETE
      FROM {$tbname_1}
      where date = ?
      AND voter_person_id = ?
EOM;
    $prepare = $dbh->prepare($sql);
    $prepare->bindValue(1, $date, PDO::PARAM_STR);
    $prepare->bindValue(2, $fromSession, PDO::PARAM_STR);
    $prepare->execute();

    $sql_insert_vote = <<< EOM
      INSERT INTO {$tbname_1}
      ( date,
        time,
        voter_person_id,
        types_of_votes,
        rank,
        voted_person_id )
      VALUES (?, ?, ?, ?, ?, ?)
EOM;
    for ($i = 1; $i < 4 ; ++$i) {
      $voted_person_id = $_POST["cn$i"];
      $prepare = $dbh->prepare($sql_insert_vote);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $fromSession, PDO::PARAM_STR);
      $prepare->bindValue(4, 'P', PDO::PARAM_STR);
      $prepare->bindValue(5, $i, PDO::PARAM_STR);
      $prepare->bindValue(6, $voted_person_id, PDO::PARAM_INT);
      $prepare->execute();
    }
    for ($i = 1; $i < 4 ; ++$i) {
      $voted_person_id = $_POST["co$i"];
      $prepare = $dbh->prepare($sql_insert_vote);
      $prepare->bindValue(1, $date, PDO::PARAM_STR);
      $prepare->bindValue(2, $time, PDO::PARAM_STR);
      $prepare->bindValue(3, $fromSession, PDO::PARAM_STR);
      $prepare->bindValue(4, 'FG', PDO::PARAM_STR);
      $prepare->bindValue(5, $i, PDO::PARAM_STR);
      $prepare->bindValue(6, $voted_person_id, PDO::PARAM_INT);
      $prepare->execute();
    }
  } catch (Exception $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    echo 'エラー!: '.$e->getMessage().'<br/>';
    die();
  }
}
?>

  <?php include ('footer_general.php'); ?>
</body>
</html>
