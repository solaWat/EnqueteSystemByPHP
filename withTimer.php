<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>タイマー</title>
</head>
<body>
<br>
<?php

$person = file('exOrder_prz.txt');

  print"<table border='1' cellpadding='5' style='background:#F0F8FF'>";
    print"<caption>発表順";
    
    for ($i = 0; $i < count($person); $i++){ // 何位まで取得するか．
      $j = $i + 1; // 発表順を見せるための変数．
      print"<tr>";
          // for  ($h = 1; $h < 3; $h++){ // 人数分のradio表示
          // // print "<label><input type='radio' name='co$h' value='$i'>{$person[$i]}<br>\n</label>";
          // print "<td>&nbsp<input type='radio' name='cn$h' value='$i'></td>";
          // }
        print "<td>$j.{$person[$i]}</td>";
        print"</tr>";
    }
    print"</tr>";
  print"</table>";

//include('timekeeper-gh-pages/index.html');
include('timekeeper-gh-pages/index.html');
?>
<!-- <h4><a href= index.html ><font color="green"> 投票しに行く（TOP） </font></a><h4> -->
</body>
</html