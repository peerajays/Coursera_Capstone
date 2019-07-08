<!DOCTYPE html>
<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['email'])){
die ("Not logged in");
}

if(isset($_POST['cancel'])) {
header("location: index.php");
return;
}

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
&& isset($_POST['headline']) && isset($_POST['summary'])) {
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
	$_SESSION['first_name'] = $_POST['first_name'];
	$_SESSION['last_name'] = $_POST['last_name'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['headline'] = $_POST['headline'];
	$_SESSION['summary'] = $_POST['summary'];
	
	if (strlen($_SESSION['first_name'])<1 || strlen($_SESSION['last_name'])<1 ||
	strlen($_SESSION['email'])<1 || strlen($_SESSION['headline'])<1 ||
	strlen($_SESSION['summary'])<1  ) {
	$_SESSION['error'] = "All fields are required";
	header("location: add.php");
	return;
	} 
	
	else if (strpos($_SESSION['email'],'@') == false ) {
	$_SESSION['error'] = "Email address must contain @";
	header("location: add.php");
	return;
	}

	else {
	$_SESSION['success'] = "Profile added";
	header("location: index.php");
	}
}
?>
<head>
<title>Peerapong</title>
</head>
<body>
<div class="container">
<h1>Adding Profile for UMSI</h1>
<p style=color:red>
<? if(isset($_SESSION['error'])){
echo($_SESSION['error']);
unset($_SESSION['error']);
}
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>