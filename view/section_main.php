<?php if(!$seance->Authentication()): ?>
<div class="panel-Authorization"> <h5>Социальная сеть для общения</h5>
<p>Для бмена сообщениями по компьютерной сети в режиме реального времени. Здесь можно говорить о том, что хочешь, и о том, что происходит в мире. Создай аккаунт и присоединяйся</p>
<?php 
	echo "<div class='structure-Authorization'>";
	require_once('view/Authorization.php');
	echo "</div></div>";
	/*echo $marselDB->addUser("Admin","Марсель","Хисамутдинов","",1,4,"marselgmx@gmail.ru","marsel1994");
	echo $marselDB->addUser("Denis","Денис","Чужой","Очень красивый",1,3,"mas@ff.tu","qwerty");
	echo $marselDB->addUser("Salavat","Салават","Хисамутдинов","Самый крутой",1,2,"salavat@mail.ru","12345");

	echo $marselDB->addTopic("комната 1 (Обо всём)","Здесь можно обсуждать всё что угодно, оставаясь в рамках приличия.",false,false,1);
	echo $marselDB->addTopic("комната 2 (Политика)","Обсуждаем события общественной и государственной жизни.",false,false,1);
	echo $marselDB->addTopic("комната 3 (Спорт)","Все что касается спорта и здорового образа жизни.",false,false,1);*/
?>

<div class="clearfix inform-Authorization">
	<article><h5>Создай аккаунт</h5><img src='/images/technical/Account.png'><p>Зарегистрируй свой аккаунт совершенно бесплатно. В личном кабинете ты можешь в любой момент изменить свой профиль, загрузить фотографию и рассказать о себе другим пользователям.</p></article>
	<article><h5>Общайся</h5><img src='/images/technical/Communicate.png'><p>Общайся с другими пользователями при помощи моментального обмена сообщениями. Так же ты можешь использовать следующие элементы разметки для текста: выделение, усиление, курсив, зачеркнутый и т.д.</p></article>
	<article><h5>Дальнейшее развитие</h5><img src='/images/technical/Free.png'><p>В дальнейшем качество сайта будет улучшаться. Сайт будет пополняться новым функционалом, обновляться, добавляться новые фичи.</p></article>
</div>
<?php endif; ?>
<?php /*=============================================================================*/ ?>
<?php if($seance->Authentication()): ?>
<div class="notification">Добро пожаловать <strong><?=$seance->user()?></strong> </div>
<?php if($seance->Authentication() == 'unconfirmed') echo "<div class=''> аккаунт не верифицирован, чтобы использовать все привилегий, на вашу почту отправлено письмо с подтверждением <div>"; 
foreach($marselDB->query("SELECT * FROM story") as $value) echo "<article id='story_$value[story_id]' class='myStory'><h3>$value[addCaption] ($value[addDate])</h3><img src='/images/story/$value[story_id].jpg'><div>$value[addText]</div></article>";
?>

<?php endif; ?>
