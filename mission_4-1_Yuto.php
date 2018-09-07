<?php
$dsn='mysql:dbname=データベース名;host=localhost';//db接続
$user='ユーザー名';
$password="パスワード名";
$pdo=new PDO($dsn,$user,$password);

$sql="CREATE TABLE testkeijiban2"//テーブル作成
."("
."id INT(11) AUTO_INCREMENT PRIMARY KEY,"
."name char(32),"
."comment TEXT,"
."password char(32),"
."send_time DATETIME"
.");";
$stmt=$pdo->query($sql);

$edit=$_POST['edit'];//編集フォームからの送信
$not_empty_edit=!empty($edit);
$comment_password=$_POST['password'];

if($not_empty_edit)
	{
	$sql=$pdo->prepare("SELECT password FROM testkeijiban2 WHERE id=$edit");//編集フォームから送信された番号に対応するidと同じレコードのパスワードを変数に代入
	$sql->execute();
	$result=$sql->fetch();
	echo $result['password']."<br>".$comment_password;
	}
if($comment_password==$result['password'])//パスワード認証
		{
		$pass_check="true";
		$sql=$pdo->prepare("SELECT name FROM testkeijiban2 WHERE id=$edit");//編集フォームから送信された番号に対応するidと同じレコードのnameを変数に代入
		$sql->execute();
		$name_sql=$sql->fetch();
		$sql=$pdo->prepare("SELECT comment FROM testkeijiban2 WHERE id=$edit");//ほぼ上と同じで、コメントを変数に代入
		$sql->execute();
		$comment_sql=$sql->fetch();
		}




?>


<!DOCTYPE html>
<html lang="jp">
<head>
<meta charset="utf-8"/>
	<form action="mission_4-1_Yuto.php" method="post">
		お名前:
		<input type="text" name='username' value="<?php if($pass_check=="true" && $not_empty_edit){echo $name_sql['name'];}?>"><br>
		コメント:
		<input type="text" name='comment' value="<?php if($pass_check=="true" && $not_empty_edit){echo $comment_sql['comment'];}?>"><br>
		パスワード：
		<input type="text" name='password'>
		<input type="submit"><br>
		削除番号:
		<input type="text" name='delete'>
		<input type="submit" value="削除"><br>
		編集対象番号:
		<input type="text" name='edit'>
		<input type="submit" value="編集"><br>
		<input type="text" name='edit_check' value="<?php if($pass_check=="true" && $not_empty_edit){echo $edit;}?>">

	</form>
</head>
<?php



$username=$_POST['username'];
$comment=$_POST['comment'];
$comment_password=$_POST['password'];
$delete=$_POST['delete'];
$edit_check=$_POST['edit_check'];
$not_empty_edit_check=!empty($edit_check);
$not_empty_delete=!empty($delete);
$date=date("Y-m-d H-i-s");
$not_empty_username=!empty($username);
$not_empty_comment=!empty($comment);

if($not_empty_delete)//削除番号が入力されたとき
	{
	$sql=$pdo->prepare("SELECT password FROM testkeijiban2 WHERE id=$delete");//入力された番号に対応するIDと同じレコードのパスワードを取得
	$sql->execute();
	$result=$sql->fetch();
	}
if($comment_password==$result['password'])//上で取得したパスワードと入力されたパスワードを認証する
		{
		$pass_check="true";
		}

if($not_empty_edit_check && $pass_check=="true")//編集
	{
	$sql="UPDATE testkeijiban2 set name='$username',comment='$comment',send_time='$date' WHERE id=$edit_check";
	$result=$pdo->query($sql);

}elseif($not_empty_delete && $pass_check=="true")
	{
	$sql="delete FROM testkeijiban2 WHERE id=$delete";
	$result=$pdo->query($sql);
}elseif($not_empty_username && $not_empty_comment)
	{
	$sql=$pdo->prepare("INSERT INTO testkeijiban2(name,comment,password,send_time)VALUES(:name,:comment,:password,:send_time)");
	$sql->bindParam(':name',$username,PDO::PARAM_STR);
	$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
	$sql->bindParam(':password',$comment_password,PDO::PARAM_STR);
	$sql->bindParam(':send_time',$date,PDO::PARAM_STR);
	$sql->execute();
	
}elseif($not_empty_edit && $pass_check=="true")
	{
	echo "編集モード"."<br>";
}else{
	echo "名前とコメントの両方に記入してください。"."<br>";
}
$sql='SELECT * FROM testkeijiban2 ORDER BY id';
$results=$pdo->query($sql);
foreach($results as $row)
	{
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['send_time'].',';
	echo $row['password']."<br>";
	}
?>
