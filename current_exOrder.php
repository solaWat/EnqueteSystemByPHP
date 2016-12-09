<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>現在の出席者と発表順</title>
</head>
<body>


<?php

$person = file('exOrder_prz.txt');

  print"<table border='1' cellpadding='5' style='background:#F0F8FF'>";
    print"<caption>発表順";
    
    for ($i = 0; $i < count($person); $i++){ // 何位まで取得するか．
      $j = $i + 1; // 発表順を見せるための変数．
      print"<tr>";
      print "<td>$j.{$person[$i]}</td>";
      print"</tr>";
    }
    print"</tr>";
  print"</table>";


// // 投票結果表示
// print"<table border='1' cellpadding='6' style='background:white'>";
// for ($i = 0; $i < count($food); $i++) {
//   $h = $i + 1 ;
//   print "<tr>";
//   print "<td>$h</td>";
//   print "<td>{$food[$i]}</td>";
//   print "</tr>\n";
// }
// print"</table>";

?>

<br>
<br><br><br>
</body>
</html