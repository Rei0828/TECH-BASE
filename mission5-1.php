<?php

$dsn = 'mysql:dbname=XXXDB;host=localhost';
$user = 'XXXUSER';
$password = 'XXXPASSWORD';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS tbtext"
."("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name CHAR(32),"
. "comment TEXT,"
. "date TEXT,"
. "CommentPass TEXT"
.");";
$stmt = $pdo->query($sql);

if (isset($_POST["submit"])) { // 投稿ボタンがクリックされた場合
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $editID = $_POST["edit-id"];
    $CommentPass = $_POST["c-pass"];
    $TempPass = $_POST["TempPass"];
    
    if (!empty($editID)) {
        if (!empty($name) && !empty($comment) && !empty($CommentPass)) {
            $date = date("Y/m/d H:i:s");
        
            $sql = 'UPDATE tbtext SET name=:name, comment=:comment, date=:date, CommentPass=:CommentPass WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $editID, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':CommentPass', $CommentPass, PDO::PARAM_STR);
            $stmt->execute();
        }
       
    } else {
        if (!empty($name) && !empty($comment) && !empty($CommentPass)) {
            $date = date("Y/m/d H:i:s");
            
            $sql = "INSERT INTO tbtext (name, comment, date, CommentPass) VALUES (:name, :comment, :date, :CommentPass)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':CommentPass', $CommentPass, PDO::PARAM_STR);
            $stmt->execute();
        }
    }    
    
} elseif (isset($_POST["delete-submit"])) { // 削除ボタンがクリックされた場合
    $delete = $_POST["delete"];
    $DeletePass = $_POST["d-pass"];
    
    if (!empty($delete) && !empty($DeletePass)) {
        $id = $delete;
        $sql = 'delete from tbtext where id=:id and CommentPass=:CommentPass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':CommentPass', $DeletePass, PDO::PARAM_INT);
        $stmt->execute();
    }
    
} elseif (isset($_POST["edit-submit"])) { // 編集ボタンがクリックされた場合
    $edit = $_POST["edit"];
    $EditPass = $_POST["e-pass"];
    
    if (!empty($edit) && !empty($EditPass)) {
        $sql = 'SELECT * FROM tbtext WHERE id=:id AND CommentPass=:CommentPass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->bindParam(':CommentPass', $EditPass, PDO::PARAM_STR);
        $stmt->execute(); // 追加
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            if ($row['id'] == $edit && $row['CommentPass'] == $EditPass) {
                $editedName = $row['name'];
                $editedComment = $row['comment'];
                $TempPass = $EditPass;
                $EditID = $edit;
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <h1>掲示板</h1>
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php echo isset($editedName) ? $editedName : ''; ?>"><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo isset($editedComment) ? $editedComment : ''; ?>"><br>
        <input type="text" name="c-pass" placeholder="パスワード">
        <input type="submit" name="submit" value="投稿">
        <input type="hidden" name="edit-id" value="<?php echo isset($EditID) ? $EditID : ''; ?>"><br>
        <input type="hidden" name="TempPass" value="<?php echo isset($TempPass) ? $TempPass : ''; ?>">
    </form>
    <br>
    <form action="" method="post">
        <input type="text" name="delete" placeholder="削除対象番号"><br>
        <input type="text" name="d-pass" placeholder="パスワード">
        <input type="submit" name="delete-submit" value="削除">
    </form>
    <br>
    <form action="" method="post">
        <input type="text" name="edit" placeholder="編集対象番号"><br>
        <input type="text" name="e-pass" placeholder="パスワード">
        <input type="submit" name="edit-submit" value="編集">
    </form>
    <br>

    <?php
    $sql = 'SELECT * FROM tbtext';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'] . " " . $row['name'] . " " . $row['comment'] . " " . $row['date'] . "<br>";
    }
    ?>
</body>
</html>