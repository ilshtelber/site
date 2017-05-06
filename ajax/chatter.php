<?php #AJAX отправка сообщение при простом запросе
require_once '../view/include.php'; //включаем ядро сайта
require_once "../view/declare.php";
$seance = new mx\user_seance();
$error = ""; //уведомление об ошибке
$send = ''; //сообщение
$user = $seance->user(); //имя пользователя
$password = $seance->password(); //пароль
if(!$seance->Authentication()){echo "вы не авторизированны, авторизируйтесь или пройдите регистрацию"; exit();} //если решил наебнуть систему

//отправляем форму с текстовым сообщением в базу данных
if(mx\verify_chat())
{
	$message=mx\filterMessage($_POST['message']); //текстовое сообщение
	$error = $marselDB->addMessage($user, $password, $message, $_POST['top']); ////добаляет сообщение в БД, в случае чего выводим ошибку
}
else
{
	$error = "ошибка в запросе";
}

//если ошибок нет, то выводим собщение из базы данных
if($error == "")
{
	$value = $marselDB->query("SELECT user.user_id, user.user, chatter.message_id, chatter.adddate, chatter.addtext FROM user, chatter WHERE user.user_id = chatter.user_id ORDER BY chatter.message_id DESC")->fetch(); //вытаскиваем сообщению из таблицы

	$send = "<li id='message_$value[message_id]'> <h5> <a href='/id$value[user_id]' target='_blank'>$value[user]</a> ($value[adddate])</h5> <p>$value[addtext]</p> </li> \n"; //выводим результат


	//$ch = curl_init("http://$_SERVER[SERVER_NAME]/comete-server/MyComete/Long-polling.php");

	if(isset($ch))
	{
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); //отмена потока вывода
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "send=".urlencode($send)); //post запрос
		curl_exec($ch);
		curl_close($ch);
	}
}
else
{
	echo $error; //выводим ошибку
}
