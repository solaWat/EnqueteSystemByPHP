<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>発表順の編集</title>
</head>
<body>
<?php
if ($_POST['delete']) { // デリートが押されたら．
  date_default_timezone_set('Asia/Tokyo');
  $date = date('Y-m-d');

  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query("USE enquete_main");

  $sql = "DELETE FROM TestA_3_order_of_presen where date = '$date' order by order_of_presen desc limit 1";
  $st = $dbh->prepare($sql);
  $st->execute();

  // for ($i=0; $i < count($food); $i++) { 
  //   $j = $i+1;
  //   $sql = "INSERT INTO TestA_3_order_of_presen (date, time, attendee_person_id, order_of_presen) VALUES ('$date', '$time', '$food[$i]', '$j') ";
  //   //ON DUPLICATE KEY UPDATE date = '$date' 


  //   //echo "$food[$i]";
  //   //$sql = "INSERT INTO enq_table_main (date, time, exist_studentname, order_of_presen) VALUES ('$date', '$time', '$food[$i]', '$i')SET $nameA = $nameA + 3 WHERE date = '$date'"; 
  //   $st = $dbh->prepare($sql);
  //   $st->execute();




	// プレゼン順を書き換える．
	$file = file('exOrder_prz.txt');
	$a = count($file) - 1 ; 
	unset($file[$a]); // 配列の最後の要素を削除する．
	$file = array_values($file); // indexを詰める．
	file_put_contents('exOrder_prz.txt', $file); // 処理が終わったら，反映させる．

	$cgd = file('exOrder_prz.txt');
	for ($i = 0; $i < count($cgd); $i++) $cgd[$i] = rtrim($cgd[$i]); // 取り出した配列のクレンジング
	// ファシグラ順のtxt書き込み
	  $person_fg = $cgd;
	  $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
	  $person_two = $person_fg[1];
	  for ($i=0; $i < count($person_fg); $i++) { 
	    if (($person_fg[$i+2]) == null) {
	        if ($person_fg[$i+1] == null) {
	          $person_fg[$i] = $person_two;
	        }
	        else{
	          $person_fg[$i] = $person_one;
	        }
	      }
	    else{
	      $person_fg[$i] = $person_fg[$i+2];
	      }
	  }
	  $fp = fopen('exOrder_fg.txt', 'w');
	  for ($i = 0; $i < count($person_fg); $i++) {
	    fwrite($fp, $person_fg[$i] . "\n");
	  }
	  fclose($fp);

  // 投票集計のリセット．トラブル回避のため．
  $fp = fopen('enquete_prz.txt', 'w');
  for ($i = 0; $i < count($person_fg); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
  // プレゼンとファシグラ，両方の投票記録を0で上書きする．
  $fp = fopen('enquete_fg.txt', 'w');
  for ($i = 0; $i < count($person_fg); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}


if ($_POST['add']) { // 追加が押されたら．

  if ($_POST['my_id'] == null) { // ボタンはcheckedされてるので，出番ないかも．
  	exit(名前が選択されていません．);
  }

  $addname_id = $_POST['my_id'];

  date_default_timezone_set('Asia/Tokyo');
  $date = date('Y-m-d');
  $time = date('H:i:s');

  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  $dbh->query("USE enquete_main");

  $query = "SELECT order_of_presen FROM TestA_3_order_of_presen WHERE date = '$date' ORDER BY order_of_presen desc LIMIT 1";
  $st = $dbh->query("$query"); 

  foreach ($st as $row) {
  $forAddOrder = $row['order_of_presen'];
  }

  $j = $forAddOrder + 1;
  $sql = "INSERT INTO TestA_3_order_of_presen (date, time, attendee_person_id, order_of_presen) VALUES ('$date', '$time', '$addname_id', '$j') ";
  $st = $dbh->prepare($sql);
  $st->execute();




  $addname = $_POST['my_id'];
  // プレゼン順を書き換える．
  $fp = fopen('exOrder_prz.txt', 'a'); // txtへの追加書き込み処理．
  fwrite($fp, "$addname" . "\n");
  fclose($fp);

	$cgd = file('exOrder_prz.txt');
	for ($i = 0; $i < count($cgd); $i++) $cgd[$i] = rtrim($cgd[$i]); // 取り出した配列のクレンジング
	  // ファシグラ順のtxt書き込み
	  $person_fg = $cgd;
	  $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
	  $person_two = $person_fg[1];
	  for ($i=0; $i < count($person_fg); $i++) { 
	    if (($person_fg[$i+2]) == null) {
	        if ($person_fg[$i+1] == null) {
	          $person_fg[$i] = $person_two;
	        }
	        else{
	          $person_fg[$i] = $person_one;
	        }
	      }
	    else{
	      $person_fg[$i] = $person_fg[$i+2];
	      }
	  }
	  $fp = fopen('exOrder_fg.txt', 'w');
	  for ($i = 0; $i < count($person_fg); $i++) {
	    fwrite($fp, $person_fg[$i] . "\n");
	  }
	  fclose($fp);

	  // 投票集計のリセット．トラブル回避のため．
  $fp = fopen('enquete_prz.txt', 'w');
  for ($i = 0; $i < count($person_fg); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
  // プレゼンとファシグラ，両方の投票記録を0で上書きする．
  $fp = fopen('enquete_fg.txt', 'w');
  for ($i = 0; $i < count($person_fg); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}
?>


<h2>＜編集＞</h2>

<!-- addname form -->
<h3>[名前を追加する]</h3>
<form method="post" action="request_exOrder.php">
<?php

// $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
// $dbh->query("USE enquete_main");

// $query = <<< EOM
//   select studentname
//   from  TestA_2_lab_member_name
//   left join TestA_3_order_of_presen
//   on TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id
//   where TestA_3_order_of_presen.date = '$date'
//   order by TestA_3_order_of_presen.order_of_presen;
// EOM;
// $st = $dbh->query("$query"); 



// // "fiscal_year"に関しては，後で，フロントサイドからトグル？などで「年度」を選択できるようにしたい． 
// $st = $dbh->query("SELECT * FROM TestA_2_lab_member_name WHERE fiscal_year = '2016'"); 



// foreach ($st as $row) {
//   # code...
//   $name = $row['studentname'];
//   $id = $row['person_id'];
//   print "<label><input type='checkbox' name='cn[]' value='$id' checked>{$name}<br><br></label>";
// }

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');

$dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
$dbh->query("USE enquete_main");

$query = <<< EOM
  SELECT studentname, person_id
  FROM  TestA_2_lab_member_name
  LEFT JOIN TestA_3_order_of_presen
  ON TestA_2_lab_member_name.person_id = TestA_3_order_of_presen.attendee_person_id 
  AND TestA_3_order_of_presen.date = '$date'
  WHERE TestA_3_order_of_presen.attendee_person_id IS NULL
  AND fiscal_year = '2016' ;
EOM;
$st = $dbh->query("$query"); 

// where TestA_3_order_of_presen.date = '$date' 
// where TestA_3_order_of_presen.attendee_person_id is null

foreach ($st as $row) {
  $name = $row['studentname'];
  $id = $row['person_id'];
  print "<label><input type='radio' name='my_id' value='$id' checked>{$name}<br><br></label>";
}




// print"<table border='1' cellpadding='6' style='background:white'>";
// $i = 1;
// foreach ($st as $row) {
//   print "<tr>";
//   print "<td>$i</td>";
//   print "<td>{$row['studentname']}</td>";
//   print "</tr>\n";
//   $i = $i + 1;
// }
// print"</table>";






// 研究室所属メンバー
$personOrsn = array(
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
$file = file('exOrder_prz.txt');
for ($i = 0; $i < count($file); $i++) $file[$i] = rtrim($file[$i]); // 取り出した配列のクレンジング
$person = array_diff($personOrsn, $file); // 研究室所属メンバーと，出席していたメンバーとの，差分を取る．
$person = array_values($person); // 配列の要素の削除後には，indexを詰める必要がある．
$person[] = '[ゲスト]'; // 配列の最後にゲストを仕込む．array_diffで消えずに，ずっと残る．

// すでに発表順に入っていた者以外の名前を表示する．
// for ($i = 0; $i < count($person); $i++) {
//   print "<label><input type='radio' name='my_id' value='$person[$i]' checked>{$person[$i]}<br><br></label>";
// }
?>
<input type="submit" name="add" value="決定" >
</form>

<!-- delete form -->
<br><h3>[名前を削除する]（※一名ずつ）</h3>
<form method="post" action="request_exOrder.php">
<input type="submit" name="delete" value="DELETE" >
</form>


<br>
<h2>＜現在の設定＞</h2>
<?php include('current_exOrder.php'); ?>

<h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
<p><a href= index.html > TOP </a></p><br><br>
</body>
</html>