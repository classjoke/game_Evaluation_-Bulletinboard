<?php
session_start();

header("Content-type: text/html; charset=utf-8");
require_once("pdo.php");
$pdo = pdo_connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$errors = Array();
if (isset($_POST["submit"])){
if(empty($_POST["mail"])) {
    $errors['mail_check'] = "メールアドレスが入力されていません";
}else{
	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
		
		/*
		ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
		$errors['member_check'] = "このメールアドレスはすでに利用されております。";
		*/
	}
}
if(count($errors) == 0){
	try{
	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "https://tb-220016.tech-base.net/Mission6/Mission_6-1-SignUp.php"."?urltoken=".$urltoken;

	$stmt = $pdo->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");
	$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
	$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
	$stmt->execute();

	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	$_SESSION["mail_address"] = $mail;
	$_SESSION["url"] = $url;
	require 'send_test.php';
}
else{
    echo $errors['mail_check'];
}
}
?>
 <!DOCTYPE html>
 <html lang="ja">
 <head>
	 <meta charset="UTF-8">
	 <meta name="viewport" content="width=device-width, initial-scale=1.0">
	 <link rel="stylesheet" href="css/style.css">
	 <title>メール登録画面</title>
 </head>
 <body>
	 <h1>メール登録画面</h1>
	 <div>
		 新規登録にはメール認証が必要です<br>
		 メールアドレスを入力してください<br>
	 </div>
	 <form action="" method="post">
		 <p>メールアドレス: <input type="text" name="mail" size="50"></p>
		 <input type="hidden" name="token" value="<?=$token?>">
		 <input type="submit" name="submit" value="認証メールを送信する">
	 </form>
 </body>
 </html>

