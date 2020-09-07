<?php
    require_once("pdo.php");
    $pdo = pdo_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!empty($_POST["submit"])){
        $errors =Array();
        if(!empty($_POST["board_name"])){
            try{
                $board_name= $_POST["board_name"];
                if (preg_match('#[\\\:?<>|　 ]|\.{1,2}/#', $board_name)) {
                    echo "掲示板名に特殊文字を含まないでください";
                    echo "<a href="."Mission_6-1-Main.php"."></a>";
                    $errors["board_name_error"] = "掲示板名が不正です";
                }
                else{
                $url = "Mission_6-1-board.php"."?boardname=".$board_name;
                $sql="CREATE TABLE $board_name"
                ."("
                ."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
                ."comment VARCHAR(128),"
                ."name VARCHAR(128),"
                ."date DATETIME NOT NULL"
                .")"
                ."ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
                $stmt = $pdo->query($sql);
                }
            }catch(PDOException $e){
                $errors["create_board_error"] = $e->getMessage();
            }
        }
        if(count($errors) === 0){
            try{
                $sql = "INSERT INTO template_board (board_title, created_date, lastModified, board_url) VALUES(:board_name, now(), now(), :url)";
                $stmt = $pdo -> prepare($sql);
                $stmt ->bindValue(":board_name", $board_name, PDO::PARAM_STR);
                $stmt ->bindValue(":url", $url, PDO::PARAM_STR);
                $stmt ->execute();
            }catch(PDOException $e){
                $errors["pdo_error"] = $e->getMessage();
            }
        }

        if(count($errors) === 0){
            echo "掲示板が作成されました！";
        }else{
            //var_dump($errors);
        }
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>掲示板作成画面</title>
</head>
<body>
    
    <form action="" method="POST">
        <input type="text" name="board_name" placeholder="掲示板タイトルを入力">
        <input type="submit" name="submit">
    </form>
</body>
</html>