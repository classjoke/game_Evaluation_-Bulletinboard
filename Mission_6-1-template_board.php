<?php
    require_once("pdo.php");
    $pdo = pdo_connect();
    $sql = "CREATE TABLE template_board"
    ."("
    ."board_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
    ."board_url VARCHAR(128) DEFAULT 'Mission_6-1-Main.php',"
    ."board_title VARCHAR(50) DEFAULT 'title',"
    ."created_date DATETIME NOT NULL,"
    ."lastModified DATETIME NOT NULL"
    .")"
    ."ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
    $stmt = $pdo->query($sql);
?>

