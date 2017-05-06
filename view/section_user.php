<?php #этот файле выводит ользователей 
if(isset($_GET['id']))
{
	$user = $marselDB->outUser($_GET['id']); //выводим пользователя по id, если передали GET запрос с ключом id
	if(!$user) 
	{
	echo "пользователь не найден"; //выводим сообзения если пользователя не нашли
	} 
	if($user)
	{ ?>
		<div class="clearfix user-menu">
			<h2><?=$user['user']?></h2>
		</div>
		<div class="clearfix row">
			<div class="OneRow_left1 structure_avatar"> <img src="/images/avatar/avatar.php?user=<?=$user['user_id']?>" alt=''> </div>
			<div class="TwoRow_left1">
				<ui class="specialListStyle List-information">
				<li><b>Имя:</b> <?=$user['name']?></li>
				<li><b>Фамилия:</b> <?=$user['surname']?></li>
				<li><b>Дата регистраций:</b> <?=$user['registration_date']?></li>
				<li><b>пол:</b> <?=$user['sex'] == 'female'?'мужской':'женский'?></li>
				<li><b>о себе: </b><?=$user['about']?></li>
				</ui>
			</div>
		</div>
	<?php }
}
else
{
	$users = $marselDB->outUser(); //выводим пользователей, если небыло переданно специальный GET запрос с ключом id
	echo "<ul>"; //спски, начла тега
	foreach($users as $value) echo "<li> <a href='id$value[user_id]''> $value[user] </a>  </li> \n"; //выводим, че
	echo "</ul>"; //закрываем теги
}
?>