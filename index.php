<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>
<form method="post" action="index.php">
以下の2つの表から，それぞれ自分以外の名前を選んで，投票を行ってください．<br><br>
<?php

$person = file('exOrder_prz.txt');// file関数：txtの中身を簡単に吸ってこれる
$person_fg = file('exOrder_fg.txt'); // 現状，プレゼンとファシグラで異なる投票データベーすを使用している．

// // デバッグ用
// foreach ($person as $l) {
//   print $l . "<br>\n";
// }
// // デバッグ用

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
    for ($i = 0; $i < count($person); $i++){ // 何位まで取得するか．
      $j = $i + 1; // 発表順を見せるための変数．
      print"<tr>";
          for  ($h = 1; $h < 3; $h++){ // 人数分のradio表示
          // print "<label><input type='radio' name='co$h' value='$i'>{$person[$i]}<br>\n</label>";
          print "<td>&nbsp<input type='radio' name='cn$h' value='$i'></td>";
          }
        print "<td>&nbsp<input type='radio' name='cn$h' value='$i'></td><td>$j.{$person[$i]}</td>";
        print"</tr>";
    }
    print"</tr>";
  print"</table>";
  print"</td>";
  print"<td>";

  // ファシグラ用投票テーブル
  print"<table border='1' cellpadding='8' style='background:#F8F8FF'>";
    print"<caption>ファシとグラ";
    print"<tr>";
    print"<th>　</th><th>1位</th><th>2位</th><th>3位</th>";
    print"</tr>";
    for ($i = 0; $i < count($person); $i++){ // 何位まで取得するか．
      print"<tr>";
      print"<td>→ {$person_fg[$i]}</td>";
          for  ($h = 1; $h < 3; $h++){ // 人数分のradio表示
          print "<td>&nbsp<input type='radio' name='co$h' value='$i'></td>";
          }
        print "<td>&nbsp<input type='radio' name='co$h' value='$i'></td>";
        print"</tr>";
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

 <!-- ボタンの種類 -->
<input type="submit" name="submit" value="投票する" >
<!-- onClick="return confirm('ボタンが3つ押されていますか？同じ人に投票しようとしていませんか？')" -->
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
    exit;
  }
  if ($_POST['cn1']==$_POST['cn2'] || $_POST['cn2']==$_POST['cn3'] || $_POST['cn1']==$_POST['cn3']) {
    $error_msg = "<h4><font color='red'>※一人の人物に，重複して順位を与えることはできません（プレゼンテーション）</font></h4>";
    print $error_msg;
    exit;
  }
  if ($_POST['co1']==$_POST['co2'] || $_POST['co2']==$_POST['co3'] || $_POST['co1']==$_POST['co3']) {
    $error_msg = "<h4><font color='red'>※一人の人物に，重複して順位を与えることはできません（ファシとグラ）</font></h4>";
    print $error_msg;
    exit;
  }

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