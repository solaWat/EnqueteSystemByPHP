<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>現在の出席者と発表順</title>
</head>
<body>
<?php
date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');

$dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
$dbh->query("USE enquete_main");

$query = <<< EOM
  select studentname
  from  TestA_2_lab_member_name
  left join TestA_3_order_of_presen
  on TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id
  where TestA_3_order_of_presen.date = '$date'
  order by TestA_3_order_of_presen.order_of_presen;
EOM;
$st = $dbh->query("$query"); 

print"<table border='1' cellpadding='5' style='background:#F0F8FF'>";
$i = 1;
foreach ($st as $row) {
  print "<tr>";
  print "<td>$i</td>";
  print "<td>{$row['studentname']}</td>";
  print "</tr>\n";
  $i = $i + 1;
}
print"</table>";


// 以前のtxtファイルベースの時のもの．
// 
// $person = file('exOrder_prz.txt');
//   print"<table border='1' cellpadding='5' style='background:#F0F8FF'>";
//     print"<caption>発表順";
    
//     for ($i = 0; $i < count($person); $i++){ // 何位まで取得するか．
//       $j = $i + 1; // 発表順を見せるための変数．
//       print"<tr>";
//       print "<td>$j.{$person[$i]}</td>";
//       print"</tr>";
//     }
//     print"</tr>";
//   print"</table>";
?>
<br>
<br><br><br>
</body>
</html