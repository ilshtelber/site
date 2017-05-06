<?php
require_once('view/include.php'); //подключаем ядро
$seance = new mx\user_seance();
if(!$seance->Authentication())
{
	if(mx\verify_user())
	{
		$user=mx\filterUser($_POST['user']); //имя пользователя + фильтруем
		$pass=mx\filterPassword($_POST['pass']); //пароль + фильтруем

		if($marselDB->isUser($user,$pass) && mx\validateUser($user,$m) && mx\validatePassword($pass,$m))
		{
			$seance->identification($user,$pass); //индетифицируем пользователя, добавляя его в сеанс
			header("Location: $_SERVER[REQUEST_URI]"); //переадресация
			exit();
		}
		else 
		{
			$_SESSION["status"] = "Неверное имя пользователя или пароль";
			header("Location: http://$_SERVER[SERVER_NAME]"); //переадресация
			exit();
		}
	}

	if($_POST)
	{
		header("Location: $_SERVER[REQUEST_URI]"); //переадресация
		exit(); //выход из программы
	}

?>
	<form action="<?=""?>" method="post" id="Authorization">
		<input type="hidden" name="csrf" value="<?=mx\token(false)?>">
		<input type="text" name="user" required="где имя?" placeholder="введите ваше имя пользователя" value="" class="structure-inputText viewText">
		<input type="password" name="pass" required="где пароль?" placeholder="введите пароль" value="" class="structure-inputText viewText">
		<button type="submit" name="send" value="yes" class="viewBtn">войти</button>
		<button type="button" class="viewBtn"><a href='/register' class='specialLink'>зарегистрироваться</a></button>
		<?='<span id="error" class="error_report">'.@$_SESSION["status"].'</span>'?>
		<?php $_SESSION["status"] = ""; //обнуляем наш статус те сессию?>
	</form>
<?php 
}
else
{
}

?>