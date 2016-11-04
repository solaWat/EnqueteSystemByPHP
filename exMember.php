<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


<title>発表順</title>
<link rel="stylesheet" href="roulette.css">
</head>
<body>

<form method="post" action="exMember.php">
<h1>○出席者を選んでください</h1>
<div style="background: #ddf; width:200px; border: 1px double #CC0000; height:100％; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px;">
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

for ($i = 0; $i < count($person); $i++) {
  print "<label>
  <input type='checkbox' name='cn[]' value='$person[$i]' checked>{$person[$i]}
  <br><br></label>";
}

// $food = $_POST['cn'];
// var_dump($food);
// echo "$food";

?>
</div><br>

 <!-- ボタンの種類 -->
<input type="submit" name="sort" value="発表順を決める 　(＆　残っている投票結果をクリアする)" >
<!-- <button type="submit" name="sort" ><big>発表順を決める 　(＆　残っている投票結果をクリアする)</big></button> -->

</form>




<h1>○今日の発表順</h1>

<table border='10'>
<?php


//var_dump($food);


// // デバッグ用
// echo "$food";
// foreach($food as $foods) {
//         echo htmlspecialchars($foods) . "\n";}
// // デバッグ用

// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('exOrder.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]);

// 投票ボタン
if ($_POST['sort']) {

  $food = $_POST['cn'];
  srand(time()); //乱数列初期化 ???
  shuffle($food);

  $fp = fopen('exOrder.txt', 'w');
  for ($i = 0; $i < count($food); $i++) {
    fwrite($fp, $food[$i] . "\n");
  }
  fclose($fp);
}

// リセットボタン ただし，1クリックでは反映されない問題がある．
if ($_POST['sort']) {
  $num = file('enquete.txt'); //前回の投票結果を読み取る
  $fp = fopen('enquete.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}

// // リセットボタン ただし，1クリックでは反映されない問題がある．
// if ($_POST['submit2']) {
//   $fp = fopen('enquete.txt', 'w');
//   for ($i = 0; $i < count($person); $i++) {
//     fwrite($fp, 0 . "\n");
//   }
//   fclose($fp);
// }
// // 更新ボタン
// $rel = $_GET['reload'];
//     if ($rel == 'true') {
//       header("Location: " . $_SERVER['PHP_SELF']);
//     }
//     /*デバッグ用*/
//     // echo($_SERVER['PHP_SELF'].'<br/>');
//     // echo($_SERVER['SCRIPT_NAME'].'<br/>');


// 投票結果表示
for ($i = 0; $i < count($food); $i++) {
  $h = $i + 1 ;
  print "<tr>";
  print "<td>$h</td>";
  print "<td>{$food[$i]}</td>";
  // print "<td><table><tr>";
  // $w = $ed[$i] * 10;
  // print "<td width='$w' bgcolor='green'> </td>";
  // print "<td>{$ed[$i]} 票</td>";
  // print "</tr></table></td>";
  print "</tr>\n";
}
?>

</table><br>
<a href= index.html ><font color="orange"> 投票しに行く </font></a>
</body>
</html