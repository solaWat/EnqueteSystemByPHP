<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<META HTTP-EQUIV="Refresh" CONTENT="6"> 

<script src="roulette.js"></script>

<title>投票結果の総計表示</title>
</head>
<body>このページは自動更新されます．(6秒ごと)
<br><br><br>

<table border='1'>

<?php

$person = file('exOrder.txt');

// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('enquete.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]);



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

for ($i = 0; $i < count($ed); $i++) {
  $sum += $ed[$i];
}
$hito = $sum / 6 ;
echo "現在『 $hito 人』の投票が終わっています．";
?>
<p><font color="red">(※票の合計を6で割っているだけ)</font></p>
</table><br>
<a href= ../ > TOP </a>
</body>
</html>