<?php 
if($seance->Authentication()): echo "вы зарегестрированны"; 
else: 
#echo '<img src="http://www.2566.by/i/teh-raboti.png" alt="">';
if(mx\verify_user())
{
	//проерка внешних данных
	if(!mx\validateUser($_POST['user'],$message_user)){$_SESSION["status"] = $message_user; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if(!mx\validateSurnameName($_POST['name'],$message_name)){$_SESSION["status"] = $message_name; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if(!mx\validateSurnameName($_POST['surname'],$message_surname)){$_SESSION["status"] = $message_surname; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if(!mx\validateEmail($_POST['mail'],$message_mail)){$_SESSION["status"] = $message_mail; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if(!mx\validatePassword($_POST['pass'],$message_password)){$_SESSION["status"] = $message_password; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if(!isset($_POST['sex']) || !is_numeric(@$_POST['sex'])){$_SESSION["status"] = "вы не ввели свой пол"; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if($marselDB->query("SELECT EXISTS(SELECT * FROM user WHERE user = '$_POST[user]')")->fetch()[0]){$_SESSION["status"] = "пользователь уже зарегистрирован"; header("Location: $_SERVER[REQUEST_URI]"); exit();}
	if($marselDB->query("SELECT EXISTS(SELECT * FROM user WHERE mail = '$_POST[mail]')")->fetch()[0]){$_SESSION["status"] = "e-mail уже зарегистрирован, если это ваш e-mail то пишите сюда marselgmx@gmail.ru, будем разбираться кароче, на стрелу вызовим его"; header("Location: $_SERVER[REQUEST_URI]"); exit();}

	//фильтруем данные
	$user=mx\filterUser($_POST['user']); //имя пользователя + фильтруем
	$name=mx\filterSurnameName($_POST['name']); //нстоящее имя
	$surname=mx\filterSurnameName($_POST['surname']); //нстоящее фамилия
	$mail = filter_var($_POST['mail'],FILTER_SANITIZE_EMAIL); //фильтруем e-mail
	$password=mx\filterPassword($_POST['pass']); //пароль + фильтруем
	$sex = $_POST['sex'];

	$status = $marselDB->addUser($user,$name,$surname,"",$sex,1,$mail,$password);
	if($status == false)
	{
		$_SESSION["status"] = "ошибка в запросе";
		header("Location: $_SERVER[REQUEST_URI]"); 
		exit();
	}
	mail($mail,"Приветствую тебя $user","$surname $name, Мы рады, что вы присоединились к нам, чтобы использовать все привилегии вашего аккаунта, подтвердите свой адрес электронной почты, перейдя по этой ссылки http://$_SERVER[SERVER_NAME]/");
	$seance->identification($user,$password); //индетифицируем пользователя, добавляя его в сеанс
	header("Location: http://$_SERVER[SERVER_NAME]"); //переадресация
	exit();
}

if($_POST)
{
	header("Location: $_SERVER[REQUEST_URI]"); //переадресация
	exit(); //выход из программы
}
?>
<div class="notification">пройдите быструю регистрацию</div>
<div class="clearfix panel_registr">
	<div class="OneRow_left1">&nbsp;</div>
	<form  method="post" id="registration" class="TwoRow_left1">
		<input type="hidden" name="csrf" value="<?=mx\token(false)?>">
		<input type="text" name="user" required="поле пустое" placeholder="Введите ваше имя пользователя, никнейм (на латинском)" value="" class="structure-inputText viewText">
		<input type="text" name="name" required="где имя?" placeholder="Введите ваше имя" class="structure-inputText viewText">
		<input type="text" name="surname" required="где фамилия?" placeholder="Введите ваше фамилию" class="structure-inputText viewText">
		<input type="email" name="mail" required="где e-mail?" placeholder="Введите e-mail" value="" class="structure-inputText viewText">
		<input type="password" name="pass" required="где пароль?" placeholder="Введите пароль" value="" class="structure-inputText viewText">
		<fieldset>
			<legend>выберите ваш пол</legend>
			<label><input type="radio" name="sex" value="1">муж</label>
			<label><input type="radio" name="sex" value="2">жен</label>
		</fieldset>
		<button type="submit" name="send" value="yes" class="viewBtn">зарегистрироваться</button>
		<button type="button" class="viewBtn"><a href='<?='http://'.$_SERVER['SERVER_NAME']?>' class='specialLink'>я зарегистрирован!</a></button>
		<?='<span id="error" class="error_report">'.@$_SESSION["status"].'</span>'?>
		<?php $_SESSION["status"] = ""; //обнуляем наш статус те сессию?>
	</form>
</div>
<?php endif;
