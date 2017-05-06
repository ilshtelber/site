<?php 
require_once "../../view/include.php";//включаем ядро сайта
require_once "../../view/declare.php";//включаем ядро сайта
header("Content-type: image/jpg"); //заголовок для браузера HTTP-ответ
#header("Content-type: $user[avatar_type]");  //заголовок для браузера HTTP-ответ (запас)
if(isset($_GET['user']) && is_numeric($_GET['user']))
{

	$user = $marselDB->outUser($_GET['user']); //извлекаем данные
	if(!$user) echo readfile(__DIR__.'/error.png');
	if(!isset($user['avatar_type'])) echo readfile(__DIR__.'/no-avatar.png'); //в случае если отсуствует аватарка
	else echo $user['avatar']; //выводим данные 
}
?>