<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>発表順の編集</title>
</head>
<body>
<h2>＜編集＞</h2>

<h3>発表が最後の人の名前を消去する</h3>
<form method="post" action="request_exOrder.php">
<input type="submit" name="delete" value="最後を消去" >
</form>
<?php
if ($_POST['delete']) {

	$file = file('exOrder_prz.txt');
	//for ($i = 0; $i < count($file); $i++) $file[$i] = rtrim($file[$i]); // 取り出した配列のクレンジング
	$a = count($file) - 1 ;
	unset($file[$a]);
	file_put_contents('exOrder_prz.txt', $file);

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

  // 集計のリセット．トラブル回避のため．
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
?>

<br>
<h3>順番の最後に新しい人の名前を追加する</h3>
<form method="post" action="request_exOrder.php">
<?php
// 研究室所属メンバー
$person = array(
  "安保　建朗",
  "Ghita Athalina",
  "倉嶋　俊",
  "小林　優稀",
  "室井　健二",
  "森田　和貴",
  "渡辺　宇",
  "荒木　香名",
  "柴沢　弘樹",
  "[ゲスト]" // 有事の際は，これで凌ぐ．
  );
// リストの中から，自分の名前を選んでもらう．
for ($i = 0; $i < count($person); $i++) {
  print "<label><input type='radio' name='my_id' value='$person[$i]' checked>{$person[$i]}<br><br></label>";
}
?>
<input type="submit" name="add" value="名前を追加" >
</form>
<?php
if ($_POST['add']) {

  $addname = $_POST['my_id'];
  // プレゼン順のtxt書き込み
  $fp = fopen('exOrder_prz.txt', 'a');
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

	  // 集計のリセット．トラブル回避のため．
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
?>


<br>
<h2>＜現在の内容＞</h2>
<?php include('current_exOrder.php'); ?>

<p><a href= index.html > TOP </a></p><br><br>
</body>
</html