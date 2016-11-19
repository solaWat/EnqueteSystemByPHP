<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- 6秒ごとにページを自動更新 -->
<META HTTP-EQUIV="Refresh" CONTENT="11"> 
<title>集計結果の表示</title>
</head>
<body>
投票結果は自動的に更新されます．<br><br>
<!-- 休憩してて良いことを示す画像 -->
<img src="rest_nobita.jpg"></img><br><br><br>

<?php
$person = file('exOrder_prz.txt');
$person_fg = file('exOrder_fg.txt'); // 結局，ファシグラの並び順はこのページでは使わなくなったが，一応残しておく．

// 同フォルダ中のテキストファイルにデータを保存する仕組み
$ed = file('enquete_prz.txt'); // txtファイルに関しては，両方とも情報を取ってくるのに使う．
$ee = file('enquete_fg.txt');
for ($i = 0; $i < count($person); $i++) $ed[$i] = rtrim($ed[$i]);
for ($i = 0; $i < count($person_fg); $i++) $ee[$i] = rtrim($ee[$i]);

// index.phpでファシグラ用に並び替えた順番を，再びプレゼン順に合わせて表示するために処理．
$ee_one = $ee[count($person)-2];
  $ee_two = $ee[count($person)-1];
  for ($i = count($person); 0 <= $i ; $i--) {
    if ($i == 1) {
      $ee[1] = $ee_two;
    }
    elseif ($i == 0) {
      $ee[0] = $ee_one;
    }
    else{
      $ee[$i] = $ee[$i-2];
      }
  }

// 投票済み者の数え上げ(enquete_przベース)
for ($i = 0; $i < count($ed); $i++) {
  $sum += $ed[$i];
}
$hito = $sum / 6 ; //(3票＋2票＋1票)
$perNum = count($person);
echo "現在，$perNum 人中『 $hito 人』の投票が終わっています．"; // 票の総計を6で割っているだけである．
print"<br><br>";

// 2つのテーブルと並列表示させるための透明テーブル
print"<table>";
  print"<caption>投票結果（個人の評価）";
  print"<tr>";
  print"<td>";
  ////////////ここから
  print"<table border='1' style='background:#F0F8FF'>";
    print"<caption align='left'>　　　　　　　　プレゼン";
    // 投票結果表示
    // プレゼンテーション
    for ($i = 0; $i < count($person); $i++) {
      print "<tr>";
      print "<td style='background:white'>　{$person[$i]}　</td>";
      print "<td><table><tr>";
      $w = $ed[$i] * 10;
      print "<td width='$w' bgcolor='green'> </td>";
      print "<td>{$ed[$i]} 票</td>";
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
    for ($i = 0; $i < count($person_fg); $i++) {
      print "<tr>";
      //print "<td>{$person_fg[$i]}</td>";
      print "<td><table><tr>";
      $w = $ee[$i] * 10;
      print "<td width='$w' bgcolor='green'> </td>";
      print "<td>{$ee[$i]} 票</td>";
      print "</tr></table></td>";
      print "</tr>\n";
    }
    print"</tr>";
  print"</table>";

  print"</td>";
  print"</tr>";
print"</table>";

//リセットボタン　不測の事態に備えて．
if ($_POST['submit2']) {
  $fp = fopen('enquete_prz.txt', 'w');
  for ($i = 0; $i < count($person); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
  // プレゼンとファシグラ，両方の投票結果をリセットできる．
  $fp = fopen('enquete_fg.txt', 'w');
  for ($i = 0; $i < count($person_fg); $i++) {
    fwrite($fp, 0 . "\n");
  }
  fclose($fp);
}
?>
<br><br>
<p><font color="brue">「shift」+「command」+「4」で，範囲を指定して，投票結果をスクリーンショットしてくだい．(mac)</font></p><br><br>

<a href= index.html > TOP </a>
<br><br><br><br><br><br><br><br><br><br>
<form method="post" action="resultVis.php"><input type="submit" name="submit2" value="※押すな※　集計結果をリセット　※"></form>
<p><font color="red">管理人のつぶやき「このページはなんだか殺風景だ……」</font></p>

</body>
</html>