<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>投票システム</title>
</head>
<body>
<?php
$dsn = 'mysql:dbname=enquete_simple;host=127.0.0.1;charset=utf8';
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8', $user, $password);
    // SELECT文以降の処理では，exec関数は使用できない．
    $dbh->exec("CREATE DATABASE IF NOT EXISTS enquete_simple"); // 無ければDBを作成する．
    $dbh = new PDO($dsn, $user, $password); //　$dbh->query("USE enquete_simple"); // こっちでも良い．
// 新しくDBを作成した場合，このカラム設定を適用する．
$col_set = <<< EOM
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
    $dbh->query("CREATE TABLE IF NOT EXISTS enq_table_beta ($col_set);"); // 無ければTABLEを作成する．
    $st = $dbh->prepare("INSERT INTO enq_table_beta (ID) VALUES(1)"); // 投票用のレコードを無ければ作成．
    $st->execute();
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

<!-- 投票用に選択ボタンを表示する． -->
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
<br><br>


<?php
if ($_POST['submit']) {

  //ラジオボタン選択のエラー表示用
  if ($_POST['cn1']==null or $_POST['cn2']==null or $_POST['cn3']==null) {
    $error_msg = "<h4><font color='red'>※全ての順位を埋めてください</font></h4>";
    print $error_msg;
    exit; // エラーを検知すると，投票はデータベースに書き込まれず，集計結果も見に行けなくなる．
  }
  if ($_POST['cn1']==$_POST['cn2'] || $_POST['cn2']==$_POST['cn3'] || $_POST['cn1']==$_POST['cn3']) {
    $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません</font></h4>";
    print $error_msg;
    exit;
  }

  $a = $_POST['cn1'] + 1;
  $b = $_POST['cn2'] + 1;
  $c = $_POST['cn3'] + 1;
  $nameA = "name$a";
  $nameB = "name$b";
  $nameC = "name$c";

  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root);
  $dbh->query("USE enquete_simple");
  $st = $dbh->query("SELECT * FROM enq_table_beta");

  $sql = "UPDATE enq_table_beta SET $nameA = $nameA + 3 WHERE ID = 1"; // 1位は3票足し込む．
  $st = $dbh->prepare($sql);
  //$st->execute(array($_POST['cn1'], $_POST['cn2'], $_POST['cn3']));
  //$st->execute(array(3, 2, 1));
  $st->execute();

  $sql = "UPDATE enq_table_beta SET $nameB = $nameB + 2 WHERE ID = 1"; // 2位は2票足し込む．
  $st = $dbh->prepare($sql);
  $st->execute();
  $sql = "UPDATE enq_table_beta SET $nameC = $nameC + 1 WHERE ID = 1"; // 3位は1票足し込む．
  $st = $dbh->prepare($sql);
  $st->execute();
}

// リセットボタン 
if ($_POST['submit2']) {
  for ($i = 0; $i < count($person); $i++) {
    $h = $i + 1;
    $nameA = "name$h";
    $sql = "UPDATE enq_table_beta SET $nameA = 0 WHERE ID = 1"; // 全てを0でアップデート．
    $st = $dbh->prepare($sql);
    $st->execute();
    }
}

// 更新ボタン　ブラウザでの更新はさせたくない．
$rel = $_GET['reload'];
    if ($rel == 'true') {
      header("Location: " . $_SERVER['PHP_SELF']);
    }
?>

<?php
print"<table border='1'>"; // 投票結果を集計して，表示するためのテーブル．
  $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root);
  $dbh->query("USE enquete_simple");
  $st = $dbh->query("SELECT * FROM enq_table_beta WHERE ID = 1"); // 今は，とりあえずID＝1にしておく．
  while ($row = $st->fetch()) {
      //$name = htmlspecialchars($row['name3']);
      //$price = htmlspecialchars($row['name4']);
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

<br>
<input type="submit" name="submit2" value="集計をリセット">
</form>

<br>
<a href= ../ > TOP </a><br><br><br>
</body>
</html