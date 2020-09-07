<?php
    session_start();
    if (!isset($_SESSION["NAME"])) {
    header("Location: Mission_6-1-logout.php");
    exit;
    }
    function sort_a($res, $sort_option){
        if(strpos($sort_option, "ID") !== false){
            $sort_name = "board_id";
        }
        elseif(strpos($sort_option, "name") !== false){
            $sort_name = "board_title";
        }
        elseif(strpos($sort_option, "created") !== false){
            $sort_name = "created_date";
        }
        elseif(strpos($sort_option, "lastModified") !== false){
            $sort_name = "lastModified";
        }
        foreach((array) $res as $key => $value){
            $sort[$key] = $value[$sort_name];
        }
        if(strpos($sort_option, "asc")){
            array_multisort($sort, SORT_ASC, $res);
            }
        else{
            array_multisort($sort, SORT_DESC, $res);
            }

        return $res;
    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>メイン</title>
</head>
    <body>
        <h1>メイン画面</h1> 
        <div class="">
            <?php
                require_once("pdo.php");
                $pdo = pdo_connect();
                $delete = "Mission_6-1-delete_board.php";
                $sql= "SELECT * FROM template_board";
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                if(!empty($_POST["sort_b"])){
                    $results = sort_a($results, $_POST["sort_a"]);
                    // var_dump($io);
                }
                foreach ($results as $row)
                {
                    echo $row['board_id'].',';
                    echo "<a href=".$row['board_url'].">".$row['board_title']."</a>";
                    echo "<br>"."作成日  ：".$row['created_date'];
                    echo "<br>"."最終更新日：".$row['lastModified']."<br>";
                    echo "<a href=".$delete."?board_title=".$row['board_title'].">削除</a>";
                    echo "<hr>";
                }
            ?>
            <a href="Mission_6-1-logout.php">ログアウト</a>
        </div>
        <select name="sort_a" id="sort_a" form="sort">
            <option value="ID_asc">ID昇順</option>
            <option value="ID_des">ID降順</option>
            <option value="name_asc">掲示板名昇順</option>
            <option value="name_asc">掲示板名降順</option>
            <option value="created_asc">作成日昇順</option>
            <option value="created_des">作成日降順</option>
            <option value="lastModified_asc">更新日昇順</option>
            <option value="lastModified_des">更新日降順</option>
        </select>
        <form action="" method="post" id="sort">
                <input type="submit" name="sort_b">
        </form>
        <div class="">
        <p>ようこそ<b><?php echo htmlspecialchars($_SESSION["NAME"], ENT_QUOTES); ?></b>さん</p> 
        <a href="Mission_6-1-make_board.php">掲示板を作成する</a>
        </div>
        
    </body>
</html>
