<?php
$dbname = 'enquete_main_2';//各々の環境で変わります．
$pre_dsn = 'mysql:host=127.0.0.1;charset=utf8';
$dsn = 'mysql:host=127.0.0.1;dbname='.$dbname.';charset=utf8mb4';//各々の環境で変わります．
$user = 'root';//各々の環境で変わります．
$password = 'root';//各々の環境で変わります．

$tbname_1 = 'test_vote';
$tbname_2 = 'test_lab_member_info';
$tbname_3 = 'test_order_of_presentation';
$fiscalyear = '2016'; // 今の所はとりあえず，年度に関しては，ベタ打ちとする．

date_default_timezone_set('Asia/Tokyo');
$date = date('Y-m-d');
$time = date('H:i:s');

try {

  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
  session_start();
  session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．

  if (isset($_POST['my_id'])) { // 「inputName.php」で選ばれた名前を抽出．
      $_SESSION['my_id'] = $_POST['my_id'];
  }
  // これがあると，セッションがとってあった場合，exitになってしまう．
  // else {
  //   echo "あなたの名前がわかりません．";
  //   exit;
  // }
  $token = $_SESSION['token'];

  $fromSession = $_SESSION['my_id'];


  $dbh = new PDO( // tableがなければ作る．
    $dsn,
    $user,
    $password,
    array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    )
  );

  $sql = <<< EOM
    SELECT studentname
    FROM {$tbname_2}
    WHERE person_id = ?
    AND   fiscal_year = ?
EOM;

  $prepare = $dbh->prepare($sql);
  $prepare->bindValue(1, $fromSession, PDO::PARAM_STR);
  $prepare->bindValue(2, $fiscalyear, PDO::PARAM_STR);
  $prepare->execute();

  foreach ($prepare as $row) {
      $masters_name = $row['studentname'];
  }

  $sql_for_vote = <<< EOM
    SELECT studentname, person_id
    FROM  {$tbname_2}
    LEFT JOIN {$tbname_3}
    ON {$tbname_2}.person_id = {$tbname_3}.attendee_person_id
    WHERE {$tbname_3}.date = ?
    AND   time = (
      SELECT MAX(time)
      FROM {$tbname_3}
      WHERE date = ? )
    ORDER BY {$tbname_3}.order_of_presen;
EOM;

  $prepare_presen_order = $dbh->prepare($sql_for_vote);
  $prepare_presen_order->bindValue(1, $date, PDO::PARAM_STR);
  $prepare_presen_order->bindValue(2, $date, PDO::PARAM_STR);
  $prepare_presen_order->execute();
foreach ($variable as $key => $value) {
  # code...
}
  foreach ($prepare_presen_order as $key => $row) {
    $name_text_PRESEN[$key]      = $row['studentname'];
    // $name_text_FG[] = $row['studentname'];
    $id_text_PRESEN[$key]        = $row['person_id'];
    // $id_text_FG[]   = $row['person_id'];
  }


  function change_order_for_FG($array)
  {
    // echo "$array[2]";
      $person_fg  = $array;
      // echo "$person_fg[0]";
      // $z = count($person_fg);
      // echo "$z";
      $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
      $person_two = $person_fg[1];
      for ($i = 0; $i < count($person_fg); ++$i) {
          if (($person_fg[$i + 2]) == null) {
              if ($person_fg[$i + 1] == null) {
                  $person_fg[$i] = $person_two;
              } else {
                  $person_fg[$i] = $person_one;
              }
          } else {
              $person_fg[$i] = $person_fg[$i + 2];
          }
      }
      // echo "$person_fg[0]";
      return $person_fg;
  }
  // echo "$name_text_PRESEN[0]";
  // echo "$name_text_PRESEN[1]";
  $name_text_FG = change_order_for_FG($name_text_PRESEN);
  $id_text_FG   = change_order_for_FG($id_text_PRESEN);
  //
  // echo "$name_text_FG[0]";
  // echo "$name_text_FG[3]";

  $count = array();
  array_push($count, 'cn1', 'cn2', 'cn3');






} catch (Exception $e) {
  header('Content-Type: text/plain; charset=UTF-8', true, 500);
  echo 'エラー!: '.$e->getMessage().'<br/>';
  die();
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<div style="background: #F0FFF0;">
<title>投票システム</title>
</head>
<body>
<?php
// ini_set( 'session.save_path', '/var/tmp_enqueteSystem' );
// ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
// ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // クッキーを発行してから，(約？)30日間の有効期限を設定．
//
// session_start();
// session_regenerate_id(); // セキュリティ向上のため，セッションIDを振り直している．
//
// if (isset($_POST['my_id'])) { // 「inputName.php」で選ばれた名前を抽出．
//     $_SESSION['my_id'] = $_POST['my_id'];
// } else {
//   echo "あなたの名前がわかりません．";
//   exit;
// }
//  $token = $_SESSION['token'];
//  //echo "$token";
//  //
//
//  $fromSession = $_SESSION['my_id'];
//
//  date_default_timezone_set('Asia/Tokyo');
//   $date = date('Y-m-d');
//   $time = date('H:i:s');

  // try {
  //     $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
  //   $dbh->query('USE enquete_main');
  //
  //     $query = "SELECT studentname FROM TestA_2_lab_member_info WHERE person_id = '$fromSession' AND fiscal_year = '2016' ";
  //     $st    = $dbh->query("$query");
  //
  //     foreach ($st as $row) {
  //         $master_name = $row['studentname'];
  //     }
  // } catch (PDOException $e) {
  //     echo 'エラー!: '.$e->getMessage().'<br/>';
  //     die();
  // }

?>

<h4>こんにちは <font color='#696969'><big><?=h($masters_name)?></big></font> さん</h4>
<p>こちらの2つの表から，それぞれ投票を行ってください．</p><br>

<form method="post" action="mainVote.php">

<!-- 2つのテーブルを並列させるための透明テーブル． -->
<table>
  <tr>
    <td>
      <table border='1' cellpadding='8' style='background:#F0F8FF'>
        <caption>
          プレゼンテーション
        </caption>
        <tr>
          <th>
            1位
          </th>
          <th>
            2位
          </th>
          <th>
            3位
          </th>
          <th>

          </th>
        </tr>
        <?php $j = 0; $k = 0;?>
        <?php foreach ($name_text_PRESEN as $row): ?>
          <?php $j = $j + 1; ?>
          <?php if (strpos($id_text_PRESEN[$k], $fromSession) !== false) { ?>
          <tr>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name=<?=h($count[$h]) ?> value=<?=h($id_text_PRESEN[$k]) ?> disabled>
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name=<?=h($count[$h]) ?> value=<?h($id_text_PRESEN[$k]) ?> disabled>
            </td>
            <td>
              <?=h($j)?>.<?=h($name_text_PRESEN[$k])?>
            </td>
          </tr>
          <?php } else { ?>
          <tr>
            <?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name=<?=h($count[$h]) ?> value=<?h($id_text_PRESEN[$k]) ?> >
            </td>
            <?php } ?>
            <td>
              &nbsp
              <input type='radio' name=<?=h($count[$h]) ?> value=<?h($id_text_PRESEN[$k]) ?> >
            </td>
            <td>
              <?=h($j)?>.<?=h($name_text_PRESEN[$k])?>
            </td>
          </tr>
          <?php } ?>
          <?php $k = $k + 1; ?>
        <?php endforeach; ?>



      </table>
    </td>
    <td>
      <table border="1" cellpadding="8" style="background:#F5F5F5">
        <caption>
          ファシとグラ！
        </caption>
        <tr>
          <th>

          </th>
          <th>
            1位
          </th>
          <th>
            2位
          </th>
          <th>
            3位
          </th>
        </tr>
<?php $j = 0; $k = 0;?>
<?php foreach ($name_text_FG as $row): ?>
<?php $j = $j + 1; ?>
<?php if (strpos($id_text_FG[$k], $fromSession) !== false) { ?>
          <tr>
            <td>
<?=h($name_text_FG[$k])?>
            </td>
<?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='co<?=h($h) ?>' value=<?=h( $id_text_FG[$k]) ?> disabled>
            </td>
<?php } ?>
            <td>
              &nbsp
              <input type='radio' name='co<?=h($h) ?>' value=<?=h( $id_text_FG[$k]) ?> disabled>
            </td>
          </tr>
<?php } else { ?>
          <tr>
            <td>
<?=h($name_text_FG[$k])?>
            </td>
<?php for ($h = 1; $h < 3; ++$h) { ?>
            <td>
              &nbsp
              <input type = 'radio' name='co<?=h($h) ?>' value=<?=h( $id_text_FG[$k]) ?> >
            </td>
<?php } ?>
            <td>
              &nbsp
              <input type='radio' name='co<?=h($h) ?>' value=<?=h( $id_text_FG[$k]) ?> >
            </td>
          </tr>
<?php } ?>
<?php $k = $k + 1; ?>
<?php endforeach; ?>
      </table>
    </td>
  </tr>
</table>

<br>
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" name="submit" value="投票する" >
</form>

  <!-- <?php $j = 0; ?>
  <?php foreach ($prepare_presen_order as $row): ?>
  <?php $name_text           = $row['studentname']; ?>
  <?php $name_text_forFG[$j] = $row['studentname']; ?>
  <?php $id_text             = $row['person_id']; ?>
  <?php $id_text_forFG[$j]   = $row['person_id']; ?> -->

<?php $j = 0; ?>
<?php foreach ($prepare_presen_order as $row): ?>
<?php $j = $j + 1; ?>
<?php if (strpos($id_text, $fromSession) !== false) { ?>
  <tr>
    <?php for ($h = 1; $h < 3; ++$h) { ?>
    <td>
      &nbsp
      <!-- <input type = 'radio' name='cn<?php $h ?>' value=<?php $id_text_PRESEN[$j] ?> disabled> -->
    </td>
    <?php } ?>
    <td>
      &nbsp
      <!-- <input type='radio' name='cn<?php $h ?>' value=<?php $id_text_PRESEN[$j] ?> disabled> -->
    </td>
    <td>
      <?=h($j)?>.<?=h($name_text_PRESEN[$j])?>
    </td>
  </tr>
<?php } else { ?>
  <tr>
    <?php for ($h = 1; $h < 3; ++$h) { ?>
    <td>
      &nbsp
      <!-- <input type = 'radio' name='cn<?php $h ?>' value=<?php $id_text_PRESEN[$j] ?> disabled> -->
    </td>
    <?php } ?>
    <td>
      &nbsp
      <!-- <input type='radio' name='cn<?php $h ?>' value=<?php $id_text_PRESEN[$j] ?> disabled> -->
    </td>
    <td>
      <?=h($j)?>.<?=h($name_text_PRESEN[$j])?>
    </td>
  </tr>
<?php } ?>
<?php endforeach; ?>


  <!-- <?php $name = $row['studentname']; ?>
  <?php $id = $row['person_id']; ?>
    <label>
      <input type='radio' name='my_id' value=<?=h($id)?> checked /><?=h($name)?>
      <br /><br />
    </label>
  <?php endforeach; ?> -->
<?php

// date_default_timezone_set('Asia/Tokyo');
// $date = date('Y-m-d');
// $time = date('H:i:s');
//
// try {
//     $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
//   $dbh->query('USE enquete_main');
//
//     $query = <<< EOM
//     SELECT studentname, person_id
//     FROM  TestA_2_lab_member_info
//     LEFT JOIN TestA_3_order_of_presentation
//     ON TestA_2_lab_member_info.person_id = TestA_3_order_of_presentation.attendee_person_id
//     WHERE TestA_3_order_of_presentation.date = '$date'
//      AND time = (SELECT MAX(time) FROM TestA_3_order_of_presentation WHERE date = '$date')
//     ORDER BY TestA_3_order_of_presentation.order_of_presen;
// EOM;
//
//     $st = $dbh->query("$query");

  // foreach ($st as $row) {
  //   $name = $row['studentname'];
  //   $id = $row['person_id'];
  //   print "<label><input type='radio' name='my_id' value='$id' checked>{$name}<br><br></label>";
  // }
//
//   // 2つのテーブルを並列させるための透明テーブル．
//   echo'<table>';
//     echo'<tr>';
//     echo'<td>';
//     // プレゼンテーション用投票テーブル
//     echo"<table border='1' cellpadding='8' style='background:#F0F8FF'>";
//     echo'<caption>プレゼンテーション';
//     echo'<tr >';
//     echo'<th>1位</th><th>2位</th><th>3位</th><th>　</th>';
//     echo'</tr>';
//       //for ($i = 0; $i < count($person); $i++){ // 人数分のradio表示
//       $j = 0;
//     // foreach ($st as $row) {
//     //     $name_text           = $row['studentname'];
//     //     $name_text_forFG[$j] = $row['studentname'];
//     //     $id_text             = $row['person_id'];
//     //     $id_text_forFG[$j]   = $row['person_id'];
//     //
//     foreach ($prepare_presen_order as $row) {
//       $name_text_PRESEN[]      = $row['studentname'];
//       $name_text_FG[] = $row['studentname'];
//       $id_text_PRESEN[]        = $row['person_id'];
//       $id_text_FG[]   = $row['person_id'];
//     // }
//
//         $j = $j + 1; // 発表順を見せるための変数．
//         if (strpos($id_text, $fromSession) !== false) {
//             // 自分の名前には投票できなくするために，ボタンを無効化する．
//           echo'<tr>';
//             for ($h = 1; $h < 3; ++$h) { // 取得したい位の1つ前まで．
//             echo "<td>&nbsp<input type='radio' name='cn$h' value='$id_text' disabled></td>";
//             // print "<td>&nbsp<input type='radio' name='cn$h' value='$i' disabled></td>";
//             }
//             echo "<td>&nbsp<input type='radio' name='cn$h' value='$id_text' disabled></td><td>$j.{$name_text}</td>"; // 取得したい最後の位分
//           echo'</tr>';
//         } else {
//             echo'<tr>';
//             for ($h = 1; $h < 3; ++$h) { // 取得したい位の1つ前まで．
//             echo "<td>&nbsp<input type='radio' name='cn$h' value='$id_text'></td>";
//             }
//             echo "<td>&nbsp<input type='radio' name='cn$h' value='$id_text'></td><td>$j.{$name_text}</td>"; // 取得したい最後の位分
//           echo'</tr>';
//         }
//     }
//     echo'</tr>';
//     echo'</table>';
//     echo'</td>';
//     echo'<td>';
// } catch (PDOException $e) {
//     echo 'エラー!: '.$e->getMessage().'<br/>';
//     die();
// }
//
    // function order_of_FG($array)
    // {
    //     $person_fg  = $array;
    //     $person_one = $person_fg[0];//ファシグラは，発表者の2つ後の順番の人が担当する．
    //   $person_two   = $person_fg[1];
    //     for ($i = 0; $i < count($person_fg); ++$i) {
    //         if (($person_fg[$i + 2]) == null) {
    //             if ($person_fg[$i + 1] == null) {
    //                 $person_fg[$i] = $person_two;
    //             } else {
    //                 $person_fg[$i] = $person_one;
    //             }
    //         } else {
    //             $person_fg[$i] = $person_fg[$i + 2];
    //         }
    //     }
    //
    //     return $person_fg;
    // }
    //
    // $id_text_forFG   = order_of_FG($id_text_forFG);
    // $name_text_forFG = order_of_FG($name_text_forFG);
//
//     // ファシグラ用投票テーブル
//     echo"<table border='1' cellpadding='8' style='background:  #F5F5F5'>";
//       echo'<caption>ファシとグラ';
//       echo'<tr>';
//       echo'<th>　</th><th>1位</th><th>2位</th><th>3位</th>';
//       echo'</tr>';
      // for ($i = 0; $i < count($id_text_forFG); ++$i) { // 人数分のradio表示
      //
      //   if (strpos($id_text_forFG[$i], $fromSession) !== false) {
      //       // 自分の名前には投票できなくするために，ボタンを無効化する．
      //     echo'<tr>';
      //       echo"<td>→ {$name_text_forFG[$i]}</td>";
      //       for ($h = 1; $h < 3; ++$h) { // 取得したい位の1つ前まで．
      //       echo "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]' disabled></td>";
      //       }
      //       echo "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]' disabled></td>"; // 取得したい最後の位分
      //     echo'</tr>';
      //   } else {
      //       echo'<tr>';
      //       echo"<td>→ {$name_text_forFG[$i]}</td>";
      //       for ($h = 1; $h < 3; ++$h) { // 取得したい位の1つ前まで．
      //       echo "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]'></td>";
      //       }
      //       echo "<td>&nbsp<input type='radio' name='co$h' value='$id_text_forFG[$i]'></td>"; // 取得したい最後の位分
      //     echo'</tr>';
      //   }
      // }
//       echo'</tr>';
//     echo'</table>';
//
//     echo'</td>';
//     echo'</tr>';
//   echo'</table>';

?>

<!-- フォームを並列したい時に使うもの．
<div style="display:inline-flex">
<form><input type="text"><input type="submit"></form>
<form><input type="text"><input type="submit"></form>
</div> -->

<br>
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" name="submit" value="投票する" >
</form>

<?php
// 投票ボタン
if ($_POST['submit']) {

  //ラジオボタン選択のエラー表示用
  if ($_POST['cn1'] == null or $_POST['cn2'] == null or $_POST['cn3'] == null) {
      $error_msg = "<h4><font color='red'>※プレゼンテーションの全ての順位を埋めてください</font></h4>";
      echo $error_msg;
      exit; // エラーを検知すると，投票はデータベースに書き込まれず，集計結果も見に行けなくなる．
  }
    if ($_POST['co1'] == null or $_POST['co2'] == null or $_POST['co3'] == null) {
        $error_msg = "<h4><font color='red'>※ファシリテーション＆グラフィックスの全ての順位を埋めてください</font></h4>";
        echo $error_msg;
        exit; //die関数にしてもいいかも．
    }
    if ($_POST['cn1'] == $_POST['cn2'] || $_POST['cn2'] == $_POST['cn3'] || $_POST['cn1'] == $_POST['cn3']) {
        $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（プレゼンテーション）</font></h4>";
        echo $error_msg;
        exit;
    }
    if ($_POST['co1'] == $_POST['co2'] || $_POST['co2'] == $_POST['co3'] || $_POST['co1'] == $_POST['co3']) {
        $error_msg = "<h4><font color='red'>※一人に重複して投票することはできません（ファシとグラ）</font></h4>";
        echo $error_msg;
        exit;
    }

  // セッションを開始する
  if (!isset($_SESSION)) {
      session_start();
  }
  // セッションに入れておいたトークンを取得
  $session_token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
  // POSTの値からトークンを取得
  $token = isset($_POST['token']) ? $_POST['token'] : '';

  // トークンがない場合は不正扱い
  if ($token === '') {
      echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに入れたトークンとPOSTされたトークンの比較
  if ($token !== $session_token) {
      echo '<br><h3><a href= resultVis.php > 集計結果を見る </a></h3>';
      die("<h4><font color='red'>（※多重投票が検知されました．初回の投票以外は，集計に反映されません．）</font></h4>");
  }
  // セッションに保存しておいたトークンの削除
  unset($_SESSION['token']);

    echo "<h3><font color='blue'>投票に成功しました．集計結果を見に行きましょう．</font></h3>";

    date_default_timezone_set('Asia/Tokyo');
    $date = date('Y-m-d');
    $time = date('H:i:s');

    try {
        $dbh = new PDO('mysql:host=127.0.0.1;charset=utf8',  root, root); //各々の環境で変わります．
    $dbh->query('USE enquete_main');

        $sql = "DELETE FROM TestA_1_vote where date = '$date' AND voter_person_id = '$fromSession' ";
        $st  = $dbh->prepare($sql);
        $st->execute();

        for ($i = 1; $i < 4; ++$i) { // for Presentation
      $j                     = $i + 1;
            $voted_person_id = $_POST["cn$i"];
            $sql             = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'P', '$i', '$voted_person_id') ";
      // ON DUPLICATE KEY UPDATE date = '$date', voter_person_id = '$fromSession'
      //ON DUPLICATE KEY UPDATE date = '$date'
      //
      //$sql = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'P', '$i', '$voted_person_id') ON DUPLICATE KEY UPDATE date = '$date', voter_person_id = '$fromSession' ";

      //echo "$food[$i]";
      //$sql = "INSERT INTO enq_table_main (date, time, exist_studentname, order_of_presen) VALUES ('$date', '$time', '$food[$i]', '$i')SET $nameA = $nameA + 3 WHERE date = '$date'";
      $st = $dbh->prepare($sql);
            $st->execute();
        }

        for ($i = 1; $i < 4; ++$i) { // for FG
      $j                     = $i + 1;
            $voted_person_id = $_POST["co$i"];
            $sql             = "INSERT INTO TestA_1_vote (date, time, voter_person_id, types_of_votes, rank, voted_person_id) VALUES ('$date', '$time', '$fromSession', 'FG', '$i', '$voted_person_id') ";
            $st              = $dbh->prepare($sql);
            $st->execute();
        }
    } catch (PDOException $e) {
        echo 'エラー!: '.$e->getMessage().'<br/>';
        die();
    }
}
?>

<br>
<h3><a href= resultVis.php > 集計結果を見る </a></h3><br><br>
<a href= index.html > TOP </a>
</body></div>
</html>
