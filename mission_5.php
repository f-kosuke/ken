<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
</head>
<body>
	<?php
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//array以下はDB内の操作で発生したエラーを警告として表示してくれる設定のための要素
	
	$sql = "CREATE TABLE IF NOT EXISTS tweet_list ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"//idカラム。AUTO_INCREMENTとPRIMARY KEYはセット。
	. "name char(32),"//nameカラム。文字数は32文字固定。
	. "comment TEXT,"
	. "date DATETIME,"//投稿日時カラム。TATETIMEは日付のための型。
	. "password TEXT);";
	$stmt = $pdo ->query($sql);//テーブル作成
	
	$table = 'SELECT * FROM tweet_list';//参照するテーブルを選択
	$get = $pdo ->query($table);
	$tweets = $get ->fetchAll();//投稿された物を配列変数'tweets'として取得
	//以下は編集したコメントを投稿するときのIF文
	if (!empty($_POST['number']) and !empty($_POST['name']) and !empty($_POST['comment']) and !empty($_POST['password'])) {
		foreach($tweets as $tweet) {//
			if ($_POST['number'] == $tweet['id'] and $_POST['password'] == $tweet['password']) {
				//tweet['カラム名']で、各投稿から各項目の情報を取得できる。
				$id = $tweet['id'];
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$password = $_POST['password'];
				$date = date('Y/m/d H:i:s');//date関数で投稿日時を取得
				$sql = 'update tweet_list set name=:name, comment=:comment, password=:password, date=:date where id=:id';//UPDATEコマンド。カラム名=:代入する値
				$stmt = $pdo ->prepare($sql);
				$stmt ->bindParam(':id', $id, PDO::PARAM_STR);
				$stmt ->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt ->bindParam(':password', $password, PDO::PARAM_STR);
				$stmt ->bindParam(':date', $date, PDO::PARAM_STR);
				$stmt ->execute();//実行
			}
		}
	}//以下新規投稿のIF文
	 elseif (!empty($_POST['name']) and !empty($_POST['comment']) and !empty($_POST['password'])) {
		$sql = $pdo -> prepare("INSERT INTO tweet_list (name, comment, password, date) VALUES(:name, :comment, :password, :date)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':password', $password, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
		$name = $_POST['name'];
		$comment = $_POST['comment'];
		$password = $_POST['password'];
		$date = date('Y/m/d H:i:s');
		$sql -> execute();
	}
	//投稿削除のIF文
	if (!empty($_POST['delete']) and !empty($_POST['delete_password'])) {
		foreach($tweets as $tweet){
			if ($_POST['delete'] == $tweet['id'] and $_POST['delete_password'] == $tweet['password']) {
				$id = $tweet['id'];
				$sql = 'delete from tweet_list where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam('id', $id, PDO::PARAM_INT);
				$stmt->execute();
			}
		}
	}
	//編集する投稿指定のIF文
	if (!empty($_POST['edit']) and !empty($_POST['pass'])) {
		foreach($tweets as $tweet){
			if ($_POST['edit'] == $tweet['id'] and $_POST['pass'] == $tweet['password']) {
				//投稿フォームに渡す情報を指定
				$edit_number = $tweet['id'];
				$edit_name = $tweet['name'];
				$edit_comment = $tweet['comment'];
				$edit_pass = $tweet['password'];
			}
		}
	}
	?>
	<form action="/mission_5.php" method="post">
		名前：<input type="text" name="name" value = "<?php if (!empty($edit_name)){ echo $edit_name; } ?>"  placeholder="名前"><br />
		コメント：<input type="text" name="comment" value = "<?php if (!empty($edit_comment)){ echo $edit_comment;} ?>" placeholder="コメント"><br />
		パスワード：<input type="text" name="password" value = "<?php if (!empty($edit_pass)) {echo $edit_pass;} ?>" placeholder="パスワード"><br />
		<input type="hidden" name="number" value="<?php if(!empty($edit_number)){echo $edit_number;} ?>">
		<input type="submit" value="送信"><br /><br />
		削除対象番号：<input type="text" name="delete" placeholder="削除対象番号"><br />
		パスワード：<input type="text" name="delete_password" placeholder="パスワード"><br />
		<input type="submit" value="削除"><br />
		編集対象番号：<input type="text" name="edit" placeholder="編集対象番号"><br />
		パスワード：<input type="text" name="pass" placeholder="パスワード"><br />
		<input type="submit" value="編集">
	</form>
	
	<?php
	$table = 'SELECT * FROM tweet_list';
	$get = $pdo ->query($table);
	$tweets = $get ->fetchAll();
	foreach($tweets as $tweet){
		echo $tweet['id']. ',';
		echo $tweet['name']. ',';
		echo $tweet['comment']. ',';
		echo $tweet['date']. '<br>';
		echo "<hr>";
	}
	?>



</body>
</html>