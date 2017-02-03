<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>発表順</title>
<div style="background-color: #cff;">
</head>
<body>
<form method="post" action="exMember.php">
<h2>○出席者を選んでください</h2>
<div style="background: #ddf; width:200px; border: 1px double #CC0000; height:100％; padding-left:10px; padding-right:10px; padding-top:10px; padding-bottom:10px;">
<?php


// 研究室所属メンバー
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
</form>

<?php

print"<h2>○今日の発表順はこちら</h2>";

// // デバッグ用
// echo "$food";
// foreach($food as $foods) {
//         echo htmlspecialchars($foods) . "\n";}


// txtファイルを新たに作成する記述は，ディレクトリのパーミッションまでいじる必要がある場合が多いようで，ローカルならまだしもリモートサーバにあげる場合などに，セキュリティ上，あまり推奨されないらしい．
// このプロジェクトでは，DB用途のtxtファイルは，手動であらかじめ作っておくことにする．パーミッションもその都度，現地で変更しておく．

// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('exOrder_prz.txt');// 発表順とファシグラの順は，あらかじめ分けて記録させておく．
$ee = file('exOrder_fg.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]); //吸い取った配列のクレンジング．
for ($i = 0; $i < count($person); $i++) $ee[$i] = rtrim($ee[$i]);

// 投票ボタン
if ($_POST['sort']) {

  $food = $_POST['cn'];
  srand(time()); //乱数列初期化．冗長の可能性あり．
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

// リセット機能 今回の発表順が決められると同時に，前回の投票結果をクリアするためのもの．
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
<!-- 直下のurlをいじると，ベルの時間とテキストのデフォルト表示を変えられる．ベルの時間の実際に鳴る時間は，コードもいじる必要がある． -->
<h3><a href= withTimer.php#t1=5:00&t2=10:00&t3=20:00&m=論文輪講%20発表時間><font color="orange"> 発表用タイマー </font></a></h3>
<h4><a href= request_exOrder.php ><font color="blue"> 発表順を編集 </font>
<h4><a href= index.html ><font color="green"> TOP </font>
</a><h4>
<br><br><br>
</body>
</html