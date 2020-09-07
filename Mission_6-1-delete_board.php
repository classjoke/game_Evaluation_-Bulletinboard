<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板削除</title>
</head>
<body>
    <?php
        require_once("pdo.php");
        $pdo = pdo_connect();
        $board_title = isset($_GET["board_title"]) ? $_GET["board_title"]: NULL;
        #echo $board_title;
        $sql = "DELETE FROM template_board WHERE board_title=:board_title";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(":board_title", $board_title, PDO::PARAM_STR);
        $stmt -> execute();
        require("Mission_6-1-Main.php");
    ?>
</body>
</html>