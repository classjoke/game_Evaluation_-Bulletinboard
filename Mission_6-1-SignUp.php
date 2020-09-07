<?php
function spaceTrim ($str) {
	// 行頭
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// 末尾
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}
header("Content-type: text/html; charset=utf-8");
session_start();


$errors = Array();
$flag = Array();
$signUpMessage = "";

if(empty($_GET)) {
	header("Location: Mission_6-1-temp.php");
	exit();
}
else{
    $urltoken = isset($_GET['urltoken']) ? $_GET['urltoken'] : NULL;
    if ($urltoken == ''){
		$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
	}else{
        try{
            require_once("pdo.php");
            $pdo = pdo_connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT mail FROM pre_member WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
            $stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();
			
			//24時間以内に仮登録され、本登録されていないトークンの場合
			if( $row_count == 1 ){
				$mail_array = $stmt->fetch();
				$mail = $mail_array['mail'];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
			}
        }catch(PDOException $e){
            echo $e->getMessage();
			die();
        }
    }

}
if (!empty($_POST["signup"])) {
    if (empty($_POST["username"])) {
        $errors['difind_id'] = 'ユーザーIDが未入力です。';
    }
    if (empty($_POST["password"])) {
        $errors['difind_pass1'] = 'パスワードが未入力です。';
    }
    if (empty($_POST["password2"])) {
        $errors['difind_pass2'] = '確認用パスワードが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] == $_POST["password2"]) {
        // 入力したユーザIDとパスワードを格納
        $username =$_POST['username'];
        $password =$_POST['password'];
        $sql = "SELECT * FROM userData WHERE name=:name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $username, PDO::PARAM_STR);
        $stmt->execute();
        $count = 0;

        $results = $stmt->fetchall();
        foreach($results as $row){
            #echo $row["name"];
            $count += 1;
        }
        if($count == 0){
            try {
                $sql = "INSERT INTO userData(name, password, mail) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);

                $stmt->execute(array($username, password_hash($password, PASSWORD_DEFAULT), $mail));
                // パスワードのハッシュ化を行う
                $stmt = $pdo->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
                $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
                $stmt->execute();
                $signUpMessage = '登録が完了しました。あなたの登録IDは '. $username. ' です。パスワードは '. $password. ' です。';  // ログイン時に使用するIDとパスワード
                echo $signUpMessage;
                $flag["signup"] = 1;
            } 
            catch (PDOException $e) {
                $errors['db_error'] = 'データベースエラー';
                //$e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
                echo $e->getMessage();
            }
        }else{
            #ユーザー名がかぶっていた場合
            echo "ユーザー名が被っているため使用できません。<br>";
            echo "別のユーザー名を入力してください。<br>";
        }
    } else if($_POST["password"] != $_POST["password2"]) {
        $errors['Disagreement'] = 'パスワードに誤りがあります。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>会員登録画面</title>
</head>
<body>
    <h1>会員登録画面</h1>
    <?php if (count($errors) == 0 & count($flag) == 0): ?>
        <form action="" method="post">
        <p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
        <p>アカウント名    ：<input type="text" name="username"></p>
        <p>パスワード      ：<input type="text" name="password"></p>
        <P>確認用パスワード : <input type="text" name="password2"></p> 
        <input type="hidden" name="token" value="<?=$token?>">
        <input type="submit" name="signup"value="登録">
        </form>
    <?php elseif(count($errors) > 0): ?>
    <?php foreach($errors as $value) echo"<p>".$value."<p>"; ?>
    <?php endif;?>
    <form action="Mission_6-1_loginform.php">
        <input type="submit" value="ログインフォームへ">
    </form>

</body>
</html>
