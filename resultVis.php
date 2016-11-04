<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<META HTTP-EQUIV="Refresh" CONTENT="6"> 

<script src="roulette.js"></script>

<title>集計結果の表示</title>
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
  if ($ed) {
    # code...
  }


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
$perNum = count($person);
echo "現在，$perNum 人中『 $hito 人』の投票が終わっています．"; // 票の総計を6で割っているだけである．

//リセットボタン ただし，1クリックでは反映されない問題がある．
if ($_POST['submit2']) {
  $fp = fopen('enquete.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}


?>


</table><br><br><br>



<a href= ../ > TOP </a><br><br><br><br><br><br><br><br><br><br>
<form method="post" action="resultVis.php"><input type="submit" name="submit2" value="※押すな※　集計結果をリセット　※"></form>
<p><font color="red">管理人のつぶやき「アラートはうざい……」</font></p>
</body>
</html>