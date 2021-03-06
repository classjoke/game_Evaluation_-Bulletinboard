
<?php
    session_start();
    $errorMessage = "";
    if (isset($_POST["login"])) {
        // 1. ユーザIDの入力チェック
        if (empty($_POST["userid"])) {  // emptyは値が空のとき
            $errorMessage = 'ユーザーIDが未入力です。';
        } else if (empty($_POST["password"])) {
            $errorMessage = 'パスワードが未入力です。';
        }
        if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
            // 入力したユーザIDを格納
            $userid = $_POST["userid"];
            try {
                require_once("pdo.php");
                $pdo = pdo_connect();
                $stmt = $pdo->prepare('SELECT * FROM userData WHERE name = ?');
                $stmt->execute(array($userid));

                $password = $_POST["password"];
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (password_verify($password, $row['password'])) {
                        //入力されたパスワードとデータベースに保存されているパスワードの比較
                        session_regenerate_id(true);
                        $id = $row['id'];
                        $sql = "SELECT * FROM userData WHERE id = $id";
                        $stmt = $pdo->query($sql);
                        foreach ($stmt as $row) {
                            $row['name'];  // ユーザー名
                        }
                        $_SESSION["NAME"] = $row['name'];
                        header("Location: Mission_6-1-Main.php");  // メイン画面へ
                        exit();
                    }else {
                        $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                    }
                } else {
                    // 4. 認証成功なら、セッションIDを新規に発行する
                    // 該当データなし
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
             echo $e->getMessage();
            }
        }
    }


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ログイン</title>

</head>
<body>
<h1>ログイン画面</h1>
<form id="loginForm" name="loginForm" action="" method="POST">
            <fieldset>
                <legend>ログインフォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <label for="userid">ユーザーID</label><input type="text" id="userid" name="userid" placeholder="ユーザーIDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                <br>
                <label for="password">パスワード</label><input type="password" id="password" name="password" value="" placeholder="パスワードを入力">
                <br>
                <input type="submit" id="login" name="login" value="ログイン">
            </fieldset>
        </form>
        <br>
        <form action="Mission_6-1-SignUp.php">
            <fieldset>          
                <legend>新規登録フォーム</legend>
                <input type="submit" value="新規登録">
            </fieldset>
        </form>
</body>
</html>