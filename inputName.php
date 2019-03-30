<?php
// 基本的な変数は var_conf ファイルを参照のこと．
include ('var_conf.php');

try {

  /**
   * セッションクッキーの設定やトークンの付与など
   */
  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
  session_start(); // session_start() は、セッションを作成します。 もしくは、リクエスト上で GET, POST またはクッキーにより渡されたセッション ID に基づき現在のセッションを復帰します。

  // トークンを発行する
  $token = md5(uniqid(rand(), true));
  // トークンをセッションに保存
  $_SESSION['token'] = $token;

  // 以前にセッション登録したことがある場合，
  // 即，投票ページ(mainVote.php)に飛ぶ．
  if (isset($_SESSION['my_id'])) {
      header('Location: mainVote.php');
      exit();
  }

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
  * 研究室現所属者の情報を，DBにアクセスして保持する．
  */
  $sql = <<< EOM
   SELECT studentname, person_id
   FROM {$tbname_2}
   WHERE fiscal_year = ?
EOM;
  $prepare_memberinfo = $dbh->prepare($sql);
  $prepare_memberinfo->bindValue(1, $fiscalyear, PDO::PARAM_STR);
  $prepare_memberinfo->execute();

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
	<title>名前を選択</title>
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
  <nav class="navbar navbar-expand-sm bg-light navbar-light border-secondary">
    <ul class="navbar-nav mr-auto">
      <li class="navbar-brand">
        <a class="nav-link" href=index.html>
          <i class="fas fa-tag"></i> EnqueteSystemByPHP
        </a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item justify-content-end">
        <a class="nav-link text-warning" href=withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間>
          <i class="fas fa-clock"></i> 発表用タイマーを起動する
        </a>
      </li>
      <li class="nav-item justify-content-end">
        <a class="nav-link text-secondary" href=index.html>
          <i class="fas fa-home"></i> TOP
        </a>
      </li>
    </ul>
  </nav>
  
  
  <!--<h3>あなたの名前を教えてください．</h3>-->
  
  <div class="row">
    <div class="col text-left">
      <!--<h2>あなたの名前を教えてください</h2>-->
    </div>
  </div>
  
  <div class="row">
    <div class="col">
      
    </div>
    <div class="col-4 text-center btn">
      <h2>あなたの名前を教えてください</h2>
      <div class="card-deck">
        <div class="card">
          <div class="card-header">所属者一覧</div>
          <div class="card-body mx-auto">
            
            <!-- 自分の名前の登録は，遷移先で行われる． -->
            <form method="post" action="mainVote.php">
              <?php foreach ($prepare_memberinfo as $row): ?>
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
              <button type="button" class="btn bg-primary text-white mt-3" data-toggle="modal" data-target="#Modal_give_cokkie">
                送信
              </button>
              
              <!-- The Modal -->
              <div class="modal" id="Modal_give_cokkie">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-warning">
                      <h4 class="modal-title text-white">注意</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-left">
                      <p>名前を再度確認したのち，「続行」を押してください．</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">中止</button>
                      <button type="submit" class="btn btn-primary"  value="送信する">続行</button>
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
  </div>
  
  <div class="jumbotron text-center mt-3" style="margin-bottom:0">
    <a href='https://github.com/solaWat/EnqueteSystemByPHP'><i class="fab fa-github"></i> https://github.com/solaWat/EnqueteSystemByPHP</a>
  </div>
</body>
</html>
