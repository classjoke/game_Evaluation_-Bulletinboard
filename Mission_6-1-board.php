<?php
    require_once("pdo.php");
    $pdo =pdo_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
    $board_name_base64 = isset($_GET["boardname"]) ? $_GET["boardname"] :NULL;
    $comment = "";
    $edit_number=NULL;
    $errors = Array();
    $modified_flag= 0;
    $name = $_SESSION["NAME"];
    if($board_name_base64 === ""){
        echo "掲示板の値が見つかりません";
        die();
    }

    if($name == ""){
        echo "ログイン情報が古いです";
        die();
    }

    if(count($errors) == 0)
    {
        if(isset($_POST["submit"]))
        {
            $name = $_SESSION["NAME"];
            $comment = $_POST["comment"];
            $edit_flag = $_POST["edit_number_hidden"];
            if($edit_flag <> null)
            {   
                // エディットフラグがあるなら
                $id = $_POST["edit_number_hidden"];
                $edit_sql = "UPDATE $board_name_base64 SET comment=:comment, date=now() WHERE id=:id AND name=:name";
                $stmt = $pdo->prepare($edit_sql);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->execute();
                $modified_flag = 1;
            }
            else
            {
                // 普通の投稿
                $insert_tmp = "INSERT INTO $board_name_base64(name, comment, date) VALUES (:name, :comment, now())";
                $new_writeing = $pdo -> prepare($insert_tmp);
                $new_writeing -> bindParam (":name", $name, PDO::PARAM_STR);
                $new_writeing -> bindParam(":comment", $comment, PDO::PARAM_STR);
                $new_writeing -> execute();
                $modified_flag = 1;
            }
            $comment = " ";
            //初期化
        }
        if(!empty($_POST["remove"]))
        {
            $id = $_POST["rmnom"];
            $delete = "DELETE FROM $board_name_base64 WHERE id=:id AND name=:name";
            $stmt = $pdo->prepare($delete);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
        }
        if(!empty($_POST["edit"]))
        {
            $id = $_POST["ednom"];
            $edit_io = "SELECT * FROM $board_name_base64 WHERE id=:id AND name=:name";
            $stmt = $pdo->prepare($edit_io);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $edit_buf = $stmt->fetchAll();
            foreach ($edit_buf as $row){
                $edit_number = $row['id'];
                $comment = $row['comment'];
            }
        }
        if($modified_flag =! 0)
        {
        $update_lastModified ="UPDATE template_board SET lastModified=now() WHERE board_title=:board_title";
        $stmt = $pdo->prepare($update_lastModified);
        $stmt->bindParam(':board_title', $board_name_base64, PDO::PARAM_STR);
        $stmt->execute();
        }
        try{
        $select = "SELECT * FROM $board_name_base64";
        // echo "ID,  username,   コメント,   コメント日<br>";
        $stmt = $pdo->query($select);
        $results = $stmt->fetchAll();
        foreach ($results as $row)
        {
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
        }catch(PDOException $e){
            if($e->getMessage() == $dosent_exit);
            $errors["difind_board"] = "掲示板が見つかりませんでした";
            echo $errors["difind_board"];
            die();
        }
    }else
    {
        var_dump($errors);
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title><?php echo $board_name_base64?></title>
        <form method="POST" action="">
            コメント:<input type = "text" name ="comment" value ="<?php echo $comment?>"><br>
            削除番号:<input type = "number" name ="rmnom"><br>
            編集番号:<input type = "number" name = "ednom"><br>
            <input type = "hidden" name = "edit_number_hidden" value ="<?php echo $edit_number?>"><br>
            <input type="submit" name="submit" value="投稿">
            <input type= "submit" name= "remove" value = "削除">
            <input type= "submit" name= "edit" value = "編集">
        </form>
        <a href="Mission_6-1-Main.php">メインページへ</a>
</head>
<body>
    
</body>
</html>