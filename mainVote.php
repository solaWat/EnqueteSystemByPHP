<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>
<?php
session_start();
session_regenerate_id();

if (isset($_POST['my_id'])) {
	$_SESSION['my_id'] = $_POST['my_id'];
}

$token = $_SESSION['token'];
// echo "$token";
?>
<h4>こんにちは <font color='#696969'><big><?php echo htmlspecialchars($_SESSION['my_id']); ?></big></font> さん</h4>
<p>こちらの2つの表から，それぞれ投票を行ってください．</p><br>
<form method="post" action="mainVote.php">



</body>
</div>
</html>