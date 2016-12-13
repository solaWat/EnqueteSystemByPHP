<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>投票システム</title>
</head>
<body>
<form method="post" action="index.php">
プレゼンテーションの1〜3位を選んでください．<br>
<?php
$person = array(
  "安保　建朗",
  "Ghita Athalina",
  "倉嶋　俊",
  "小林　優稀",
  "室井　健一",
  "森田　和貴",
  "渡辺　宇",
  "荒木　香名",
  "柴沢　弘樹"
  );
for ($h = 1; $h < 4; $h++){ // 何位まで取得するか．
  print(" <br>");
  print "$h 位 \n " ;
  print("<br>");
    for ($i = 0; $i < count($person); $i++) { // 人数分のradio表示
      print "<input type='radio' name='cn$h' value='$i'>{$person[$i]}<br>\n";
    }
}
?>

<br>
 <!-- ボタンの種類 -->
<input type="submit" name="submit" value="投票">
<input type="submit" name="reload" value="更新">
<input type="submit" name="submit2" value="リセット(のち，要更新)">
</form>
<table border='1'>
<?php
// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('enquete.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]);
// 投票ボタン
if ($_POST['submit']) {
  $ed[$_POST['cn1']] += 3; // 1位から順にポイントが高くなる
  $ed[$_POST['cn2']] += 2;
  $ed[$_POST['cn3']] ++;

  $fp = fopen('enquete.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, $ed[$i] . "\n");
  }
  fclose($fp);
}
// リセットボタン 1クリックでは反映されない問題がある．
if ($_POST['submit2']) {
  $fp = fopen('enquete.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}
// 更新ボタン
$rel = $_GET['reload'];
    if ($rel == 'true') {
      header("Location: " . $_SERVER['PHP_SELF']);
    }
    /*デバッグ用*/
    // echo($_SERVER['PHP_SELF'].'<br/>');
    // echo($_SERVER['SCRIPT_NAME'].'<br/>');

// 投票結果表示
for ($i = 0; $i < count($person); $i++) {
  print "<tr>";
  print "<td>{$person[$i]}</td>";
  print "<td><table><tr>";
  $w = $ed[$i] * 10;
  print "<td width='$w' bgcolor='green'> </td>";
  print "<td>{$ed[$i]} 票</td>";
  print "</tr></table></td>";
  print "</tr>\n";
}
?>

</table>
<br>
<!-- <p><font color="red">次の課題　ルーレット結果(出席者，発表順)を取ってきて，投票対象として表示する．ファシリテーター投票につなげるのも楽．</font></p> -->
<a href= ../ > TOP </a>
</body>
</html