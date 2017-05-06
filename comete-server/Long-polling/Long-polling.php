<?php #отправка сообщений через длинные опросы Long-polling.php
require_once '../../view/include.php';//включаем ядро сайта
require_once "../../view/declare.php";
$col = $marselDB->query("SELECT count(*) FROM user, chatter WHERE user.user_id = chatter.user_id")->fetch()[0]; //кол-во сообщений
$limit = time() + 20; //время стопа
while(time() < $limit)
{
	$new_col = $marselDB->query("SELECT count(*) FROM user, chatter WHERE user.user_id = chatter.user_id")->fetch()[0]; //измененное кол-во сообщений
	if($col != $new_col)
	{
		$topic_id = $marselDB->query("SELECT topic_id FROM chatter WHERE message_id = $new_col")->fetch()[0]; //номер топика
		if($_SESSION['top'] == $topic_id)
		{
			$seance = new mx\user_seance();
			$available = $seance->Authentication(); //доступен ли вход
			$panel = $available?'<a>ответить</a>':''; //панель обычного пользователя
			$panel = ($available == 'moderator')?'<a>ответить</a> | <a>удалить сообщение</a>':"$panel"; //панель модератора
			$panel = ($available == 'admin')?'<a>ответить</a> | <a>удалить сообщение</a> | <a>заблокировать пользователя</a>':"$panel"; //панель администратора
			$avatar = '/images/technical/no-avatar.png';
			foreach($marselDB->outMessage($topic_id) as $value)
			{
			echo "<li id='message_$value[messageTopic_id]' class='clearfix'> <a href='/id$value[user_id]' target='_blank'><img src='/images/avatar/avatar.php?user=$value[user_id]' alt=''></a><h5><a href='/id$value[user_id]' target='_blank' class='specialLink'>$value[user]</a> ($value[adddate])</h5><p>$value[addtext]</p> <div></div> $panel\n";
			session_write_close();
			exit();
			}
		}
	}
usleep(10); //время ожидания  скрипта 10 сек
session_write_close();
}
exit();