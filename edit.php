<?php
require_once "pdo.php";
session_start();

if(isset($_POST['cancel'])){
header("location: index.php");
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
     && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
		|| strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1 ) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    $sql = "UPDATE profile SET first_name = :fn, last_name = :ln,
            email = :em, headline = :hl, summary = :sm
            WHERE profile_id = :profile_id" ;
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
		':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
		':sm' => $_POST['summary'],
       ':profile_id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>
<h1>Edit profile</h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60" value="<?=$fn?>"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?=$ln?>"/></p>
<p>Email:
<input type="text" name="email" size="30" value="<?=$em?>"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80" value="<?=$hl?>"/></p>
<p>Summary:<br/>
<input type = "text" name="summary"  value="<?=$sm?>"></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>