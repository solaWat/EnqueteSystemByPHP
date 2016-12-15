<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>投票システム</title>
</head>
<body>
<?php
$dsn = 'mysql:dbname=enquete_simple;host=127.0.0.1;charset=utf8';
//$dsn = 'mysql:dbname=forDataSciClass;host=127.0.0.1';
//$dsn = 'mysql:dbname=mysql;host=127.0.0.1';
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8', $user, $password);
    $dbh->exec("CREATE DATABASE IF NOT EXISTS enquete_simple"); // SELECT文以降の処理では，exec関数は使用できない．
    $dbh = new PDO($dsn, $user, $password);
    //$dbh->query("USE enquete_simple"); // こっちでも良い．

$string = <<< EOM
  ID    int(11) unsigned AUTO_INCREMENT NOT NULL,
  name1  int(11) default '0',
  name2  int(11) default '0',
  name3  int(11) default '0',
  name4  int(11) default '0',
  name5  int(11) default '0',
  name6  int(11) default '0',
  name7  int(11) default '0',
  name8  int(11) default '0',
  name9  int(11) default '0',
  PRIMARY KEY (ID)
EOM;

    $dbh->query("CREATE TABLE IF NOT EXISTS Test6 ($string);");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    // $dbh->query('SET NAMES sjis');

    // $sql = 'select * from Test5';
    // foreach ($dbh->query($sql) as $row) {
    //     print($row['id']);
    //     print($row['name1'].'<br>');
    // }


    // $sql = 'select id, name1 from Test5';
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();

    // while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    //     print($result['id']);
    //     print($result['name1'].'<br>');
    // }


    // // $sql = 'insert into Test5 (id, name1) values (?, ?)';
    // // $stmt = $dbh->prepare($sql);
    // // $flag = $stmt->execute(array(4, 3));

    // $sql = 'select * from Test5';
    // foreach ($dbh->query($sql) as $row) {
    //     print($row['id']);
    //     print($row['name1'].'<br>');
    // }


    // $sql = 'select * from Test5';
    // foreach ($dbh->query($sql) as $row) {
    //     print($row['name1']);
    //     print($row['name2'].'<br>');
    // }


    // $sql = 'select name1, name2 from Test5';
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();

    // while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
    //     print($result['name1']);
    //     print($result['name2'].'<br>');
    // }


    // // $sql = 'insert into Test5 (name1, name2) values (?, ?)';
    // // $stmt = $dbh->prepare($sql);
    // // $flag = $stmt->execute(array(5, 5));

    // $sql = 'select * from Test5';
    // foreach ($dbh->query($sql) as $row) {
    //     print($row['name1']);
    //     print($row['name2'].'<br>');
    // }






} catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}
?>

<!-- 
<table border="1">
<tr><th>名前</th><th>価格</th></tr>
<?php
  // //$pdo = new PDO("mysql:dbname=enquete_simple", "root");
  // $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root);
  // $dbh->query("USE enquete_simple");
  // $st = $dbh->query("SELECT * FROM Test5");
  // while ($row = $st->fetch()) {
  //   $name = htmlspecialchars($row['name1']);
  //   $price = htmlspecialchars($row['name2']);
  //   echo "<tr><td>$name</td><td>$price 円</td></tr>";
  // }
?>
</table>
 -->


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
      print "<label><input type='radio' name='cn$h' value='$i'>{$person[$i]}</label><br>\n";
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

if ($_POST['submit']) {

  $a = $_POST['cn1'] + 1;
  $b = $_POST['cn2'] + 1;
  $c = $_POST['cn3'] + 1;
  $nameA = "name$a";
  $nameB = "name$b";
  $nameC = "name$c";
  echo "$nameA";
  echo "$nameB";
  echo "$nameC";

  //$pdo = new PDO("mysql:dbname=men", "root");
  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root);
  $dbh->query("USE enquete_simple");
  $st = $dbh->query("SELECT * FROM Test6");
  //$st = $pdo->prepare("INSERT INTO udon VALUES(?,?)");
  $st = $dbh->prepare("INSERT INTO Test6 (ID) VALUES(1)");
  $st->execute();

  //$sql = "insert into Test5 (".$nameA.", ".$nameB.", ".$nameC.") values (?, ?, ?)";
  $sql = "UPDATE Test6 SET $nameA = $nameA + 3 WHERE ID = 1";
  echo "$sql";

  //$sql = "insert into Test5 ('{$nameA}', '{$nameB}', '{$nameC}') values (?, ?, ?)";
  $st = $dbh->prepare($sql);
  //$st->execute(array($_POST['cn1'], $_POST['cn2'], $_POST['cn3']));
  //$st->execute(array(3, 2, 1));
  $st->execute();

  $sql = "UPDATE Test6 SET $nameB = $nameB + 2 WHERE ID = 1";
  $st = $dbh->prepare($sql);
  $st->execute();
  $sql = "UPDATE Test6 SET $nameC = $nameC + 1 WHERE ID = 1";  
  $st = $dbh->prepare($sql);
  $st->execute();

  

print"<table border='1'>";
//print"<tr><th>名前</th><th>価格</th></tr>";
  //$pdo = new PDO("mysql:dbname=enquete_simple", "root");
  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root);
  $dbh->query("USE enquete_simple");
  $st = $dbh->query("SELECT * FROM Test6 WHERE ID = 1");
  while ($row = $st->fetch()) {
    $name = htmlspecialchars($row['name3']);
    $price = htmlspecialchars($row['name4']);
    //echo "<tr><td>$name</td><td>$price 円</td></tr>";
    for ($i = 0; $i < count($person); $i++) {
      $h = $i +1;
      print "<tr>";
      print "<td>{$person[$i]}</td>";
      print "<td><table><tr>";
      $w = $row["name$h"] * 10;
      print "<td width='$w' bgcolor='green'> </td>";
      print "<td>{$row["name$h"]} 票</td>";
      print "</tr></table></td>";
      print "</tr>\n";
    }
  }
print"</table>";




  // $stmt = $dbh->prepare("INSERT INTO Test5 (ID, name1, name2, name3, name4, name5, name6, name7, name8, name9) VALUES (2, :name1, :name2, :name3, :name4, :name5, :name6, :name7, :name8, :name9)");
  // $stmt -> bindValue(':name . $_POST['cn1']', += 3, PDO::PARAM_INT);
  // $stmt -> bindValue(':name . $_POST['cn2']', :name . $_POST['cn2'] + 2, PDO::PARAM_INT);
  // $stmt -> bindValue(':name . $_POST['cn3']', :name . $_POST['cn3'] + 1, PDO::PARAM_INT);
  // $stmt->execute();

  //$stmt = $dbh -> prepare("INSERT INTO Test5 ('ID', name1', 'name2') VALUES (1, 1, 2);");
  // $stmt->bindValue(':name1', 1, PDO::PARAM_INT);
  // $stmt->bindValue(':name2', 2, PDO::PARAM_INT);

  //$name = 'one';
  //$stmt->execute();

  //UPDATE test_table SET point = point+1 WHERE id = $id;
}


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

  for ($i = 0; $i < count($person); $i++) {
    
    $h = $i +1;
    $nameA = "name$h";
    $sql = "UPDATE Test6 SET $nameA = 0 WHERE ID = 1";  
    $st = $dbh->prepare($sql);
    $st->execute();
    }
  // $sql = "UPDATE Test5 SET $nameA = 0 WHERE ID = 27";  
  // $st = $dbh->prepare($sql);
  // $st->execute();
  // $sql = "UPDATE Test5 SET $nameB = $nameB + 2 WHERE ID = 27";  
  // $st = $dbh->prepare($sql);
  // $st->execute();
  // $sql = "UPDATE Test5 SET $nameC = $nameC + 1 WHERE ID = 27";  
  // $st = $dbh->prepare($sql);
  // $st->execute();


  // $fp = fopen('enquete.txt', 'w');
  // for ($i = 0; $i < count($person); $i++) {
  //   fwrite($fp, 0 . "\n");
  // }
  // fclose($fp);
}
// 更新ボタン
$rel = $_GET['reload'];
    if ($rel == 'true') {
      header("Location: " . $_SERVER['PHP_SELF']);
    }
    /*デバッグ用*/
    // echo($_SERVER['PHP_SELF'].'<br/>');
    // echo($_SERVER['SCRIPT_NAME'].'<br/>');

// // 投票結果表示
// for ($i = 0; $i < count($person); $i++) {
//   print "<tr>";
//   print "<td>{$person[$i]}</td>";
//   print "<td><table><tr>";
//   $w = $ed[$i] * 10;
//   print "<td width='$w' bgcolor='green'> </td>";
//   print "<td>{$ed[$i]} 票</td>";
//   print "</tr></table></td>";
//   print "</tr>\n";
// }
?>

</table>
<br>
<!-- <p><font color="red">次の課題　ルーレット結果(出席者，発表順)を取ってきて，投票対象として表示する．ファシリテーター投票につなげるのも楽．</font></p> -->
<a href= ../ > TOP </a>
</body>
</html