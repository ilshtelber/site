<?php
$user = $marselDB->outUser($marselDB->query("SELECT user_id FROM user WHERE user ='".$seance->user()."'")->fetch()[0]); //извлекаем пользователя
if(isset($_POST['send']) && $_POST['send'] == 'yes' && $_POST['csrf'] == mx\token(false,$_POST['csrf']))
{
	$status = false;
	$message_name = '';
	$message_surname = '';
	$message_avatar = '';
	print_r($_FILES['avatar']);

	if(mx\validateSurnameName($_POST['name'],$message_name)) $status = $marselDB->updateUser($seance->user(),$seance->password(),'name',mx\filterSurnameName($_POST['name'])); //обновляем имя пользователя
	if(mx\validateSurnameName($_POST['surname'],$message_surname)) $status = $marselDB->updateUser($seance->user(),$seance->password(),'surname',mx\filterSurnameName($_POST['surname'])); //обновляем фамилию пользователя
	if(isset($_POST['aboutme']) && $_POST['aboutme'] != $user['about']) $status = $marselDB->updateUser($seance->user(),$seance->password(),'about',mx\filterMessageAbout($_POST['aboutme'])); //обновляем информацию о себе
	if(isset($_FILES['avatar']) && mx\validateFile($_FILES['avatar'], $message_avatar)) $status = $marselDB->updateUser($seance->user(),$seance->password(),'avatar',$_FILES['avatar']); //обновляем фотографию (аватарку) пользователя

	if(!$status) 
	{
		
		//if($message_name != '' || $message_surname != '') $_SESSION["status"] = $message_name."<br>  ".$message_surname;
		if($message_avatar != '') $_SESSION["status"] = $message_avatar;
		if($message_avatar == 'файл не загружен') $_SESSION["status"] = 'вы ввели некорректные данные, попробуйте ещё раз';
	}
	else
	{
		header("Location: http://$_SERVER[SERVER_NAME]/about"); 
		exit();
	}
}

//используем внутренний редирект для пост запроса
if($_POST)
{
	header("Location: $_SERVER[REQUEST_URI]"); //переадресация
	exit(); //выход из программы
}

if($seance->Authentication() && $user):

	//выход из учетной записи
	if(isset($_GET['option']) && $_GET['option'] == "exit") 
	{
		$seance->exit();
		header("Location: http://$_SERVER[SERVER_NAME]"); //переадресация
		exit(); //выход из программы
	}
	//настроки
	if(isset($_GET['option']) && $_GET['option'] == "setup") 
	{?>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="csrf" value="<?=mx\token(false)?>">
			<input type="hidden" name="MAX_FILE_SIZE" value="65530">
			<div class="clearfix"><h5 class="OneRow_left2">Имя:</h5><input type="text" name="name" placeholder="Введите ваше имя" class="structure-inputText TwoRow_left1 viewText"></div>
			<div class="clearfix"><h5 class="OneRow_left2">Фамилия:</h5><input type="text" name="surname" placeholder="Введите ваше фамилию" class="structure-inputText TwoRow_left1 viewText"></div>
			<div class="clearfix"><h5 class="OneRow_left2">фотография:</h5><label class="TwoRow_left1"><input type="file" name="avatar"></label></div>
			<div class="clearfix"><h5>о себе:</h5><textarea name="aboutme" class="structure-inputText viewText"><?=$user['about']?></textarea></div>
			<button type="submit" name="send" value="yes" class="viewBtn">отправить</button>
			<?='<span class="error_report">'.@$_SESSION["status"].'</span>'?>
			<?php $_SESSION["status"] = ""; //обнуляем наш статус те сессию?>
		</form>
	<?php }
	else
	{?>
		<div class="clearfix user-menu">
			<h2><?=$user['user']?></h2>
			<button class="viewBtn"><a href='/about/exit'>выход</a></button>
			<button class="viewBtn"><a href='/about/setup'>настройки</a></button>
		</div>

		<div class="clearfix row">
			<div class="OneRow_left1 structure_avatar"> <img src="/images/avatar/avatar.php?user=<?=$user['user_id']?>" alt=''> </div>
			<div class="TwoRow_left1">
				<ui class="specialListStyle List-information">
				<li><b>Имя:</b> <?=$user['name']?><a href='/about/setup'> сменить имя</a></li>
				<li><b>Фамилия:</b> <?=$user['surname']?><a href='/about/setup'> сменить фамилию</a></li>
				<li><b>Дата регистраций:</b> <?=$user['registration_date']?></li>
				<li><b>e-mail:</b> <?=$user['mail']?></li>
				<li><b>пол:</b> <?=$user['sex'] == 'female'?'мужской':'женский'?><a href='http://medportal.ru/clinics/services/491/'> сменить пол</a></li>
				<li><b>о себе:</b> <?=$user['about']?></li>
				</ui>
			</div>
		</div>
	<?php }
else:
	header("Location: http://$_SERVER[SERVER_NAME]/register"); //переадресация
	exit(); //выход из программы
endif;