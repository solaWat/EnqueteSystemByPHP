<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>
<?php
// ini_set( 'session.save_path', '/var/tmp_enqueteSystem' );
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．

session_start();
session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．

if(isset($_POST['my_id'])){ // 「inputName.php」で選ばれた名前を抽出．
    $_SESSION['my_id'] = $_POST['my_id'];
}
 $token = $_SESSION['token'];
 //echo "$token";
 
 $fromSession = $_SESSION['my_id'];
 
 date_default_timezone_set('Asia/Tokyo');
  $date = date('Y-m-d');
  $time = date('H:i:s');

  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query("USE enquete_main");

  $query = "SELECT studentname FROM TestA_2_lab_member_name WHERE person_id = '$fromSession' AND fiscal_year = '2016' ";
  $st = $dbh->query("$query"); 

  foreach ($st as $row) {
  $master_name = $row['studentname'];
  }
?>

<h4>こんにちは <font color='#696969'><big><?php echo htmlspecialchars($master_name); ?></big></font> さん</h4>
<p>こちらの2つの表から，それぞれ投票を行ってください．</p><br>

<form method="post" action="mainVote.php">
<?php
$person = file('exOrder_prz.txt');// file関数：txtの中身を簡単に吸ってこれる
$person_fg = file('exOrder_fg.txt'); // 現状，プレゼンとファシグラで異なる投票データベースを使用している．

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

$dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
$dbh->query("USE enquete_main");

$query = <<< EOM
  SELECT studentname, person_id
  FROM  TestA_2_lab_member_name
  LEFT JOIN TestA_3_order_of_presen
  ON TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id
  WHERE TestA_3_order_of_presen.date = '$date'
   AND time = (SELECT MAX(time) FROM TestA_3_order_of_presen WHERE date = '$date')
  ORDER BY TestA_3_order_of_presen.order_of_presen;
EOM;

$st = $dbh->query("$query"); 

// foreach ($st as $row) {
//   $name = $row['studentname'];
//   $id = $row['person_id'];
//   print "<label><input type='radio' name='my_id' value='$id' checked>{$name}<br><br></label>";
// }


// 2つのテーブルを並列させるための透明テーブル．
print"<table>";
  print"<tr>";
  print"<td>";
  // プレゼンテーション用投票テーブル
  print"<table border='1' cellpadding='8' style='background:#F0F8FF'>";
    print"<caption>プレゼンテーション";
    print"<tr >";
    print"<th>1位</th><th>2位</th><th>3位</th><th>　</th>";
    print"</tr>";
    //for ($i = 0; $i < count($person); $i++){ // 人数分のradio表示
    $j = 0;
    foreach ($st as $row ) {
      $name_text = $row['studentname'];
      $name_text_forFG[$j] = $row['studentname'];
      $id_text = $row['person_id'];
      $id_text_forFG[$j] = $row['person_id'];

      $j = $j + 1; // 発表順を見せるための変数．
      if (strpos($id_text, $fromSession) !== false) {// 自分の名前には投票できなくするために，ボタンを無効化する．
        print"<tr>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='cn$h' value='$id_text' disabled></td>";
          // print "<td>&nbsp<input type='radio' name='cn$h' value='$i' disabled></td>";
          }
        print "<td>&nbsp<input type='radio' name='cn$h' value='$id_text' disabled></td><td>$j.{$name_text}</td>"; // 取得したい最後の位分
        print"</tr>";
      }else{
        print"<tr>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='cn$h' value='$id_text'></td>";
          }
        print "<td>&nbsp<input type='radio' name='cn$h' value='$id_text'></td><td>$j.{$name_text}</td>"; // 取得したい最後の位分
        print"</tr>";
      }
    }
    print"</tr>";
  print"</table>";
  print"</td>";
  print"<td>";


  function order_of_FG($array){
    $person_fg = $array;
    $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
    $person_two = $person_fg[1];
    for ($i=0; $i < count($person_fg); $i++) { 
      if (($person_fg[$i+2]) == null) {
          if ($person_fg[$i+1] == null) {
            $person_fg[$i] = $person_two;
          }
          else{
            $person_fg[$i] = $person_one;
          }
        }
      else{
        $person_fg[$i] = $person_fg[$i+2];
        }
    }
    return $person_fg;
  }

  $id_text_forFG = order_of_FG($id_text_forFG);
  $name_text_forFG = order_of_FG($name_text_forFG);


  // ファシグラ用投票テーブル
  print"<table border='1' cellpadding='8' style='background:  #F5F5F5'>";
    print"<caption>ファシとグラ";
    print"<tr>";
    print"<th>　</th><th>1位</th><th>2位</th><th>3位</th>";
    print"</tr>";
    for ($i = 0; $i < count($id_text_forFG); $i++){ // 人数分のradio表示

      if (strpos($id_text_forFG[$i], $fromSession) !== false) {// 自分の名前には投票できなくするために，ボタンを無効化する．
        print"<tr>";
        print"<td>→ {$name_text_forFG[$i]}</td>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]' disabled></td>";
          }
        print "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]' disabled></td>"; // 取得したい最後の位分
        print"</tr>";
      }else{
        print"<tr>";
        print"<td>→ {$name_text_forFG[$i]}</td>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]'></td>";
          }
        print "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]'></td>"; // 取得したい最後の位分
        print"</tr>";
      }
    }
    print"</tr>";
  print"</table>";

  print"</td>";
  print"</tr>";
print"</table>";
?>

<!-- フォームを並列したい時に使うもの．
<div style="display:inline-flex">
<form><input type="text"><input type="submit"></form>
<form><input type="text"><input type="submit"></form>
</div> -->

<br>
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" name="submit" value="投票する" >
</form>

<?php
// 投票ボタン
if ($_POST['submit']) {

  // 同フォルダ中のテキストファイルにデータを保存する仕組み
  $ed = file('enquete_prz.txt'); //　まず開く．あらかじめ，現在のtxtファイルの内容を記憶しておく．理由は後述．
  $ee = file('enquete_fg.txt');
  for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]); // 取り出した配列のクレンジング
  for ($i = 0; $i < count($person); $i++) $ee[$i] = rtrim($ee[$i]);

  //ラジオボタン選択のエラー表示用
  if ($_POST['cn1']==null or $_POST['cn2']==null or $_POST['cn3']==null) {
    $error_msg = "<h4><font color='red'>※プレゼンテーションの全ての順位を埋めてください</font></h4>";
    print $error_msg;
    exit; // エラーを検知すると，投票はデータベースに書き込まれず，集計結果も見に行けなくなる．
  }
  if ($_POST['co1']==null or $_POST['co2']==null or $_POST['co3']==null) {
    $error_msg = "<h4><font color='red'>※ファシリテーション＆グラフィックスの全ての順位を埋めてください</font></h4>";
    print $error_msg;
    exit; //die関数にしてもいいかも．
  }
  if ($_POST['cn1']==$_POST['cn2'] || $_POST['cn2']==$_POST['cn3'] || $_POST['cn1']==$_POST['cn3']) {
    $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（プレゼンテーション）</font></h4>";
    print $error_msg;
    exit;
  }
  if ($_POST['co1']==$_POST['co2'] || $_POST['co2']==$_POST['co3'] || $_POST['co1']==$_POST['co3']) {
    $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（ファシとグラ）</font></h4>";
    print $error_msg;
    exit;
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
    echo "<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>";
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに入れたトークンとPOSTされたトークンの比較
  if ($token !== $session_token) {
    echo "<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>";
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに保存しておいたトークンの削除
  unset($_SESSION['token']);

  
  print "<h3><font color='blue'>投票に成功しました．集計結果を見に行きましょう．</font></h3>";

  date_default_timezone_set('Asia/Tokyo');
  $date = date('Y-m-d');
  $time = date('H:i:s');

  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query("USE enquete_main");

  $sql = "DELETE FROM TestA_1_vote where date = '$date' AND voter_person_id = '$fromSession' ";
  $st = $dbh->prepare($sql);
  $st->execute();

  for ($i=1; $i < 4; $i++) { // for Presentation
    $j = $i+1;
    $voted_person_id = $_POST["cn$i"];
    $sql = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'P', '$i', '$voted_person_id') ";
    // ON DUPLICATE KEY UPDATE date = '$date', voter_person_id = '$fromSession'
    //ON DUPLICATE KEY UPDATE date = '$date' 
    //
    //$sql = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'P', '$i', '$voted_person_id') ON DUPLICATE KEY UPDATE date = '$date', voter_person_id = '$fromSession' ";


    //echo "$food[$i]";
    //$sql = "INSERT INTO enq_table_main (date, time, exist_studentname, order_of_presen) VALUES ('$date', '$time', '$food[$i]', '$i')SET $nameA = $nameA + 3 WHERE date = '$date'"; 
    $st = $dbh->prepare($sql);
    $st->execute();
  }

  for ($i=1; $i < 4; $i++) { // for FG
    $j = $i+1;
    $voted_person_id = $_POST["co$i"];
    $sql = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'FG', '$i', '$voted_person_id') ";
    $st = $dbh->prepare($sql);
    $st->execute();
  }
}
?>

<br>
<h3><a href= resultVis.php > 集計結果を見る </a></h3><br><br>
<a href= index.html > TOP </a>
</body></div>
</html>