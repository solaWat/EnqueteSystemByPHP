<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script src="roulette.js"></script>

<title>投票システム</title>
</head>
<body>
<form method="post" action="index.php">
プレゼンテーションの1〜3位を選んでください．<br>
<?php

// echo $_POST['body']; //post受信用
// $ed2 = file('person.txt');
// if ($_POST['body']) {

//   $fp2 = fopen('person.txt', 'w');
//   //for ($i = 0; $i < count($person); $i++) {
//     fwrite($fp2, body . "\n");
//   //}
//   fclose($fp);
// }


// $fp = fopen("exOrder.txt", "r");
// while ($person = fgets($fp)) {
//   echo "$person<br />";
// }
//fclose($fp);


$person = file('exOrder.txt');

// // デバッグ用
// foreach ($person as $l) {
//   print $l . "<br>\n";
// }
// // デバッグ用


// $person = array(
//   "安保　建朗",
//   "Ghita Athalina",
//   "倉嶋　俊",
//   "小林　優稀",
//   "室井　健一",
//   "森田　和貴",
//   "渡辺　宇",
//   "荒木　香名",
//   "柴沢　弘樹"
//   );

for ($h = 1; $h < 4; $h++){ // 何位まで取得するか．
  print(" <br>");
  print "$h 位 \n " ;
  print("<br>");
    for ($i = 0; $i < count($person); $i++) { // 人数分のradio表示
      print "<label><input type='radio' name='cn$h' value='$i'>{$person[$i]}<br>\n</label>";
    }
}


?>
<br>
 <!-- ボタンの種類 -->
<input type="submit" name="submit" value="投票">
<input type="submit" name="reload" value="更新">

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
// リセットボタン ただし，1クリックでは反映されない問題がある．
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

</table><br>
<form method="post" action="index.php"><input type="submit" name="submit2" value="※重要※　総計結果をリセット"></form>

<p><font color="red">　</font></p>
<a href= resultVis.php > 総計画面へ行く </a><br>
<a href= ../ > TOP </a>
</body>
</html>