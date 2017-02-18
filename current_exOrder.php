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
  SELECT studentname
  FROM  TestA_2_lab_member_name
  LEFT JOIN TestA_3_order_of_presen
  ON TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id
  WHERE TestA_3_order_of_presen.date = '$date'
   AND time = (SELECT MAX(time) FROM TestA_3_order_of_presen WHERE date = '$date')
  ORDER BY TestA_3_order_of_presen.order_of_presen;
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

?>
<br>
<br><br><br>
</body>
</html