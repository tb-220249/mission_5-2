<?php

    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード名';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //編集番号と投稿番号が同じで、パスワードと編集パスワードが同じならば、その番号の情報を取得する。
    if(!empty($_POST["e_num"])){
        $sql = 'SELECT * FROM mission5 WHERE id=:id AND password=:password';
        
        $id=$_POST["e_num"];
        $editpass=$_POST["e_pass"];
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $editpass, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $row = $results[0];
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <h1>Web掲示板</h1>
    <form action="" method="post">
        <input type="txt" name="name" placeholder="名前"
        value=<?PHP
                if(!empty($_POST["e_num"])){
                    echo $row['name'];
                }
                ?>><br>
        <input type="txt" name="comment" placeholder="コメント"
        value=<?PHP
                if(!empty($_POST["e_num"])){
                    echo $row['comment'];
                }
              ?>
        ><br>
        <input type="txt" name="pass" placeholder="パスワード">
        <input type="submit" name="submit"><br><br>
        <input type="hidden" name="id_num"
        value=<?PHP
                if(!empty($_POST["e_pass"])){
                    echo $row['id'];
                }
              ?>
        >
        
        <input type="txt" name="del_num" placeholder="削除対象番号"><br>
        <input type="txt" name="del_pass" placeholder="パスワード" >
        <input type="submit" name="del_submit" value="削除"><br><br>
        
        <input type="txt" name="e_num" placeholder="編集対象番号"><br>
        <input type="txt" name="e_pass" placeholder="パスワード" >
        <input type="submit" name="e_submit" value="編集"><br><br>
    </form>
    <?php
    	
        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS mission5"  
    	." ("
    	. "id INT AUTO_INCREMENT PRIMARY KEY,"  
    	. "name char(32),"  
        . "comment TEXT,"
        . "date TEXT,"
        . "password TEXT"  
    	.");";
    	$stmt = $pdo->query($sql);  
        
        
        //送信ボタンが押されたときの操作
        if(!empty($_POST["submit"])){            
            //新規投稿
            if(empty($_POST["id_num"])){    
                if(empty($_POST["name"])){
                    echo '<font color="red">名前を入力してください<br><br></font>';
                }elseif(empty($_POST["comment"])){
                    echo '<font color="red">コメントを入力してください<br><br></font>';
                }elseif(empty($_POST["pass"])){
                    echo '<font color="red">送信パスワードを入力してください<br><br></font>';
                }else{
                    //データの入力
                    $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");  
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);  
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
                    $name=$_POST["name"];
                    $comment=$_POST["comment"];
                    $date=date("Y/m/d H:i:s");
                    $pass=$_POST["pass"];
                    $sql -> execute();
                        echo "投稿されました<br><br>";
            
                }
            //編集機能
            }else{
                if(!empty($_POST["pass"])){
                  
                    $edit_pass=$_POST["pass"];
                    $id=$_POST["id_num"];
                    $name=$_POST["name"];
                    $comment=$_POST["comment"];
                    $date=date("Y/m/d H:i:s");

                    $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date, password=:password WHERE id=:id'; 
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $edit_pass, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    echo "編集されました<br><br>";
                }//ifの終わり
            }//elseの終わり
        }//ifの終わり

        //削除機能
        if(!empty($_POST["del_submit"])){ 
            if(empty($_POST["del_num"]) && empty($_POST["del_pass"])){
                    echo '<font color="red">削除したい番号を入力してください<br><br></font>';
            }elseif(!empty($_POST["del_num"]) && empty($_POST["del_pass"])){
                    echo '<font color="red">削除パスワードを入力してください<br><br></font>';
            }elseif(!empty($_POST["del_num"]) && !empty($_POST["del_pass"])){
                                    
                //削除パスワードがあっていたら、削除を実行し、削除が実行されましたと表示したい。
                //削除パスワードが間違っていたら、削除をせず、削除パスワードが無効ですと表示したい。
                $sql = 'SELECT * FROM mission5 WHERE id=:id'; 
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $id=$_POST["del_num"];
                $stmt->execute();
                $results = $stmt->fetchAll();
                $row = $results[0];
            
                    if($row['password']==$_POST["del_pass"]){
                            $del_pass=$_POST["del_pass"];
                            $id = $_POST["del_num"];

                            $sql = 'DELETE FROM mission5 WHERE id=:id AND password=:password'; 
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->bindParam(':password', $del_pass, PDO::PARAM_STR);
                            $stmt->execute();
                      
                            echo "削除が実行されました<br><br>";
                        }else{
                            echo '<font color="red">削除パスワードが無効です<br><br></font>';
                        }//elseの終わり
            }//elseifの終わり
        }//ifの終わり
        
        //編集ボタンが押されたときの機能
        if(!empty($_POST["e_submit"])){
            
            if(empty($_POST["e_num"])){
                echo '<font color="red">編集したい番号を入力してください<br><br></font>';
            }elseif(empty($_POST["e_pass"])){
                echo '<font color="red">編集パスワードを入力してください<br><br></font>';
            }elseif(!empty($_POST["e_num"]) && !empty($_POST["e_pass"])){
                $sql = 'SELECT * FROM mission5 WHERE id=:id'; 
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $id=$_POST["e_num"];
                $stmt->execute();
                $results = $stmt->fetchAll();
                $row = $results[0];
            
                    if($row['password']==$_POST["e_pass"]){
                            echo "編集が承諾されました<br><br>";
                        }else{
                            echo '<font color="red">編集パスワードが無効です<br><br></font>';
                        }//elseの終わり
            }//elseifの終わり
        }//ifの終わり
        ?>
        <span style="color:dodgerblue; background-color:azure; font-size:20px;">投稿一覧↓↓↓<br></span>

    <?php
        //ブラウザへの表示機能
        $sql = 'SELECT * FROM mission5'; 
        $stmt = $pdo->query($sql);                   
        $results = $stmt->fetchAll();              
        	foreach ($results as $row){            
        		echo $row['id'].',';               
        		echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
        	echo "<hr>";
    	}        
    ?>
</body>
</html>