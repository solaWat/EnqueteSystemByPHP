<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>
<?php
session_start();
session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．

if(isset($_POST['my_id'])){ // 「inputName.php」で選ばれた名前を抽出．
    $_SESSION['my_id'] = $_POST['my_id'];
}
 $token = $_SESSION['token'];
 //echo "$token";
?>
<h4>こんにちは <font color='#696969'><big><?php echo htmlspecialchars($_SESSION['my_id']); ?></big></font> さん</h4>
<p>こちらの2つの表から，それぞれ投票を行ってください．</p><br>

<form method="post" action="mainVote.php">
<?php
$person = file('exOrder_prz.txt');// file関数：txtの中身を簡単に吸ってこれる
$person_fg = file('exOrder_fg.txt'); // 現状，プレゼンとファシグラで異なる投票データベースを使用している．

// // デバッグ用
// foreach ($person as $l) {print $l . "<br>\n";}

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
    for ($i = 0; $i < count($person); $i++){ // 人数分のradio表示
      $j = $i + 1; // 発表順を見せるための変数．
      if (strpos($person[$i], $_SESSION['my_id']) !== false) {// 自分の名前には投票できなくするために，ボタンを無効化する．
        print"<tr>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          // print "<label><input type='radio' name='co$h' value='$i'>{$person[$i]}<br>\n</label>";
          print "<td>&nbsp<input type='radio' name='cn$h' value='$i' disabled></td>";
          }
        print "<td>&nbsp<input type='radio' name='cn$h' value='$i' disabled></td><td>$j.{$person[$i]}</td>"; // 取得したい最後の位分
        print"</tr>";
      }else{
        print"<tr>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='cn$h' value='$i'></td>";
          }
        print "<td>&nbsp<input type='radio' name='cn$h' value='$i'></td><td>$j.{$person[$i]}</td>"; // 取得したい最後の位分
        print"</tr>";
      }
    }
    print"</tr>";
  print"</table>";
  print"</td>";
  print"<td>";

  // ファシグラ用投票テーブル
  print"<table border='1' cellpadding='8' style='background:  #F5F5F5'>";
    print"<caption>ファシとグラ";
    print"<tr>";
    print"<th>　</th><th>1位</th><th>2位</th><th>3位</th>";
    print"</tr>";
    for ($i = 0; $i < count($person); $i++){ // 人数分のradio表示

      if (strpos($person_fg[$i], $_SESSION['my_id']) !== false) {// 自分の名前には投票できなくするために，ボタンを無効化する．
        print"<tr>";
        print"<td>→ {$person_fg[$i]}</td>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='co$h' value='$i' disabled></td>";
          }
        print "<td>&nbsp<input type='radio' name='co$h' value='$i' disabled></td>"; // 取得したい最後の位分
        print"</tr>";
      }else{
        print"<tr>";
        print"<td>→ {$person_fg[$i]}</td>";
          for  ($h = 1; $h < 3; $h++){ // 取得したい位の1つ前まで．
          print "<td>&nbsp<input type='radio' name='co$h' value='$i'></td>";
          }
        print "<td>&nbsp<input type='radio' name='co$h' value='$i'></td>"; // 取得したい最後の位分
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
  $ed = file('enquete_prz.txt'); //　まず開く．
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
  else{
    $error_msg = "<h4><font color='blue'>投票に成功しました．ブラウザの更新はしないでください．</font></h4>";
    print $error_msg;
  }



  // セッションを開始する
if (!isset($_SESSION)) {
  session_start();
}
// セッションに入れておいたトークンを取得
$session_token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
// POSTの値からトークンを取得
$token = isset($_POST['token']) ? $_POST['token'] : '';

// echo "$session_token";
// echo "<br>";
// echo "$token";

// トークンがない場合は不正扱い
if ($token === '') {
    die("不正な処理ですよ。1");
}
// セッションに入れたトークンとPOSTされたトークンの比較
if ($token !== $session_token) {
    die("不正な処理ですよ。2");
}
// セッションに保存しておいたトークンの削除
unset($_SESSION['token']);



  // 投票の重み付け
  // プレゼンテーション用
  $ed[$_POST['cn1']] += 3; // 1位から順にポイントが高くなる
  $ed[$_POST['cn2']] += 2;
  $ed[$_POST['cn3']] ++;

  $fp = fopen('enquete_prz.txt', 'w'); // txtを開いて書き込み，正確には足しこみ．
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, $ed[$i] . "\n");
  }
  fclose($fp); 

  // ファシグラ用
  $ee[$_POST['co1']] += 3; // 1位から順にポイントが高くなる
  $ee[$_POST['co2']] += 2;
  $ee[$_POST['co3']] ++;

  $fp = fopen('enquete_fg.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, $ee[$i] . "\n");
  }
  fclose($fp);
}
?>

<br><br>
<h3><a href= resultVis.php > 集計結果を見る </a></h3><br><br>
<a href= index.html > TOP </a>
</body></div>
</html>