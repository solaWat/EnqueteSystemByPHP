<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- 11秒ごとにページを自動更新 -->
<META HTTP-EQUIV="Refresh" CONTENT="11"> 
<title>集計結果の表示</title>
</head>
<body>
お疲れ様でした．投票結果は自動的に更新されます．<br><br>
<!-- 休憩してて良いことを示す画像 -->
<img src="rest_nobita.jpg"></img><br><br><br>

<?php

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

$dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
$dbh->query("USE enquete_main");

$query = <<< EOM
  SELECT studentname, person_id
  FROM  TestA_2_lab_member_name
  LEFT JOIN TestA_3_order_of_presen
  ON TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id
  WHERE TestA_3_order_of_presen.date = '$date'
   AND time = (SELECT MAX(time) FROM TestA_3_order_of_presen WHERE date = '$date')
  ORDER BY TestA_3_order_of_presen.order_of_presen;
EOM;
$st = $dbh->query("$query");

// $query = "SELECT studentname FROM TestA_2_lab_member_name WHERE person_id = '$fromSession' AND fiscal_year = '2016' ";
// $st = $dbh->query("$query"); 

foreach ($st as $row) {
  $attendee_studentname[] = $row['studentname'];
  $hoge[] = $row['person_id'];
}

$attendee_person_number = count($attendee_studentname);
// $stmt = $pdo -> query("SELECT * FROM テーブル名");
// $count = $stmt -> rowCount();


// P票の集計
for ($i=0; $i < count($hoge); $i++) { 
  $one_person_id = $hoge[$i];
// なんかサブクエリがうまくいかない
$query = <<< EOM
    SELECT 
      COUNT(rank = '1' or null) AS rank1_num,
      COUNT(rank = '2' or null) AS rank2_num,
      COUNT(rank = '3' or null) AS rank3_num
    FROM TestA_1_vote
    WHERE date = '$date'
     AND types_of_votes = 'P'
     AND voted_person_id = '$one_person_id' ;
EOM;
  $st = $dbh->query("$query"); 

  foreach ($st as $row) {
    $sum_voted_P[] = ($row['rank1_num'] * 3)+($row['rank2_num'] * 2)+($row['rank3_num'] * 1);
    // $sum_voted_P[] = $row['sum_voted'];
  }
}

// FG票の集計
for ($i=0; $i < count($hoge); $i++) { 
  $one_person_id = $hoge[$i];
// なんかサブクエリがうまくいかない
$query = <<< EOM
    SELECT 
      COUNT(rank = '1' or null) AS rank1_num,
      COUNT(rank = '2' or null) AS rank2_num,
      COUNT(rank = '3' or null) AS rank3_num
    FROM TestA_1_vote
    WHERE date = '$date'
     AND types_of_votes = 'FG'
     AND voted_person_id = '$one_person_id' ;
EOM;
  $st = $dbh->query("$query"); 

  foreach ($st as $row) {
    $sum_voted_FG[] = ($row['rank1_num'] * 3)+($row['rank2_num'] * 2)+($row['rank3_num'] * 1);
    // $sum_voted_P[] = $row['sum_voted'];
  }
}


$query = <<< EOM
    SELECT DISTINCT voter_person_id
    FROM TestA_1_vote
    WHERE date = '$date' ;
EOM;
$st = $dbh->query("$query"); 
foreach ($st as $row) {
  $forSUM[] = $row['voter_person_id'];
  $finish_vote_num = count($forSUM);
}


echo "現在，$attendee_person_number 人中『 $finish_vote_num 人』の投票が終わっています．";
print"<br><br>";

// 2つのテーブルと並列表示させるための透明テーブル
print"<table>";
  print"<caption>投票結果 ( $date )";
  print"<tr>";
  print"<td>";
  ////////////ここから
  print"<table border='1' style='background:#F0F8FF'>";
    print"<caption align='left'>　　　　　　　　プレゼン";
    // 投票結果表示
    // プレゼンテーション
    for ($i = 0; $i < count($hoge); $i++) {
      print "<tr>";
      print "<td style='background:white'>　{$attendee_studentname[$i]}　</td>";
      print "<td><table><tr>";
      $w = $sum_voted_P[$i] * 10;
      print "<td width='$w' bgcolor='green'> </td>";
      print "<td>{$sum_voted_P[$i]} 票</td>";
      print "</tr></table></td>";
      print "</tr>\n";
    }
    print"</tr>";
  print"</table>";
  /////////////ここまで，一つのカタマリ

  print"</td>";
  print"<td>";

  print"<table border='1' style='background:#F5F5F5'>";
    print"<caption>ファシグラ";
    print"<tr>";
    // ファシグラ
    for ($i = 0; $i < count($hoge); $i++) {
      print "<tr>";
      //print "<td>{$person_fg[$i]}</td>";
      print "<td><table><tr>";
      $w = $sum_voted_FG[$i] * 10;
      print "<td width='$w' bgcolor='green'> </td>";
      print "<td>{$sum_voted_FG[$i]} 票</td>";
      print "</tr></table></td>";
      print "</tr>\n";
    }
    print"</tr>";
  print"</table>";

  print"</td>";
  print"</tr>";
print"</table>";

// //リセットボタン　不測の事態に備えて．
// if ($_POST['submit2']) {
//   $fp = fopen('enquete_prz.txt', 'w');
//   for ($i = 0; $i < count($person); $i++) {
//     fwrite($fp, 0 . "\n");
//   }
//   fclose($fp);
//   // プレゼンとファシグラ，両方の投票結果をリセットできる．
//   $fp = fopen('enquete_fg.txt', 'w');
//   for ($i = 0; $i < count($person_fg); $i++) {
//     fwrite($fp, 0 . "\n");
//   }
//   fclose($fp);
// }
?>
<br><br>
<p><font color="brue">「shift」+「command」+「4」で，範囲を指定して，投票結果をスクリーンショットしてください．(mac)</font></p><br><br>

<a href= index.html > TOP </a>
<br><br><br><br><br><br><br><br><br><br>
<!-- <form method="post" action="resultVis.php"><input type="submit" name="submit2" value="※押すな※　集計結果をリセット　※"></form> -->
<p><font color="red">管理人のつぶやき「なんか，全体的に殺風景だ……」</font></p>

</body>
</html>