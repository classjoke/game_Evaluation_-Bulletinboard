<?php
session_start();

if (isset($_SESSION["NAME"])) {
    $errorMessage = "ログアウトしました。";
} else {
    $errorMessage = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>ログアウト</title>
</head>
<body>
    <h1>ログアウト画面</h1>
    <div><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></div>
    <ul>
        <li><a href="Mission_6-1_loginform.php">ログイン画面に戻る</a></li>
    </ul>
</body>
</html>