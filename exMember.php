<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>発表順</title>
<!-- 柴沢くんのroulette.jsの見た目に似せるために，このcssファイルを引用している -->
<link rel="stylesheet" href="roulette.css">
</head>
<body>
<form method="post" action="exMember.php">
<h2>○出席者を選んでください</h2>
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
// チェックボックスで今日の出席者を選んでもらう．
for ($i = 0; $i < count($person); $i++) {
  print "<label><input type='checkbox' name='cn[]' value='$person[$i]' checked>{$person[$i]}<br><br></label>";
}
?>

</div><br>
 <!-- 出席者から今日の発表順がソートされる時点で，これより前の投票結果を削除する．投票の反映は上書きではなく，足し込みのため． -->
<input type="submit" name="sort" value="発表順を決める 　(＆　残っている投票結果をクリアする)" >
<!-- <button type="submit" name="sort" ><big>発表順を決める 　(＆　残っている投票結果をクリアする)</big></button> -->
</form>

<?php

print"<h2>○今日の発表順</h2>";

// // デバッグ用
// echo "$food";
// foreach($food as $foods) {
//         echo htmlspecialchars($foods) . "\n";}


// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('exOrder_prz.txt');// 発表順とファシグラの順は，あらかじめ分けて記録させておく．
$ee = file('exOrder_fg.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]); //吸い取った配列のクレンジング．
for ($i = 0; $i < count($person); $i++) $ee[$i] = rtrim($ee[$i]);

// 投票ボタン
if ($_POST['sort']) {

  $food = $_POST['cn'];
  srand(time()); //乱数列初期化 ???
  shuffle($food); //　出席者をランダムソートにかけ，発表順を決める．

  // プレゼン順のtxt書き込み
  $fp = fopen('exOrder_prz.txt', 'w');
  for ($i = 0; $i < count($food); $i++) {
    fwrite($fp, $food[$i] . "\n");
  }
  fclose($fp);

  // ファシグラ順のtxt書き込み
  $person_fg = $food;
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
}

// リセットボタン 今回の発表順が決められると同時に，前回の投票結果をクリアするためのもの．
if ($_POST['sort']) {
  $fp = fopen('enquete_prz.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
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

// ルーレット表現を取り込みたかった．
// var roulette = setInterval(function(){
// var text = Math.floor(Math.random() * 100);
// $(セレクタ名).text(text);
// },50);
// setTimeout(function(){
// clearInterval(roulette);
// },3000);


// 投票結果表示
print"<table border='1' cellpadding='6' style='background:white'>";
for ($i = 0; $i < count($food); $i++) {
  $h = $i + 1 ;
  print "<tr>";
  print "<td>$h</td>";
  print "<td>{$food[$i]}</td>";
  print "</tr>\n";
}
print"</table>";
?>

<br>
<h4><a href= index.html ><font color="green"> 投票しに行く（TOP） </font></a><h4>
</body>
</html