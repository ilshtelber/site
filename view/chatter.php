<?php #в этом файле исполняется чат и выводтся сообщении пользователей
	$user = $seance->user(); //пользователь
	$password = $seance->password(); //пароль пользователя
	$available = $seance->Authentication(); //доступен ли вход
	$topic_id = $_GET['top'];

	if($available): //доступен ли пользователю комната
	//если отправляем форму с текстовым сообщением, то есть была нажата кнопка "отправить" то...
	if(mx\verify_chat() && $available)
	{
		$message=mx\filterMessage($_POST['message']); //текстовое сообщение
		$error = $marselDB->addMessage($user,$password,$message,$topic_id); //добаляет сообщение в БД, в случае чего выводим ошибку
		$_SESSION["status"] = $error; //в случае неудачи, если сообщения не отправилось, добавляем в сессию ошибку о состояния пользователя
	}

	//используем внутренний редирект для пост запроса
	if($_POST)
	{
		header("Location: $_SERVER[REQUEST_URI]"); //переадресация
		exit(); //выход из программы
	}
?>
<form action="<?=""?>" method="post" enctype="multipart/form-data" id="chat">
	<input type="hidden" name="csrf" value="<?=mx\token(false)?>">
	<textarea name="message" class="structure-inputText viewText" placeholder="введите текстовое сообщение" value=""></textarea>
	<button type="submit" name="send" value="yes" class="viewBtn">отправить</button>
	<!--<input type="file" name="myfile">-->
	<?='<span id="error" class="error_report">'.@$_SESSION["status"].'</span>'?>
	<?php $_SESSION["status"] = ""; //обнуляем наш статус те сессию?>
</form>
<?php 
	else: 
	echo '<div class="notification"> Вы не авторизированны, авторезируйтесь, если у вас отсутствует учетная запись <a href="/register1">то пройдите регистрацию</a> </div>';
	echo '<div class="structure-Authorization">';
	require_once('view/Authorization.php');
	echo '</div>';
	endif;
?>

<div><ul class="content-chat specialListStyle" id="content-chat">
<?php 
$panel = $available?'<a>ответить</a>':''; //панель обычного пользователя
$panel = ($available == 'moderator')?'<a>ответить</a> | <a>удалить сообщение</a>':"$panel"; //панель модератора
$panel = ($available == 'admin')?'<a>ответить</a> | <a>удалить сообщение</a> | <a>заблокировать пользователя</a>':"$panel"; //панель администратора
foreach($marselDB->outMessage($topic_id) as $value) echo "<li id='message_$value[messageTopic_id]' class='clearfix'> <a href='/id$value[user_id]' target='_blank'><img src='/images/avatar/avatar.php?user=$value[user_id]' alt=''></a><h5><a href='/id$value[user_id]' target='_blank' class='specialLink'>$value[user]</a> ($value[adddate])</h5><p>$value[addtext]</p> <div></div> $panel\n"; ?>
</ul></div>
<div id="upp"><a href="#top">вверх</a></div>

<script>
var url = 'http://<?=$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']?>/ajax/chatter.php'; //скрипт

//DOM объекты
var documentError = $('#error'); //<span> где вывод сообщения
var documentForm = $('#chat'); //форма
var chat = $('#content-chat'); //контейнер для сообщений
<?php if($available): ?>
/*============================================отправка сообщений AJAX===============================================*/
var req = new XMLHttpRequest(); //AJAX

//функция для отправки сообщений где формировуем POST запрос
var SendRequest = function(){
	var post = new FormData(document.getElementById('chat')); //post запрос где кодирует формы для отправки на сервер 

    if (!req) return; //Проверяем существование запроса еще раз
    post.append("send", "yes"); //добавляем ещё один post запрос
    post.append("top", <?=$_GET['top']?>); //добавляем ещё один post запрос

	req.open('POST',url, true); //открываем соеденение и настраиваем асинхронный запрос
	req.timeout = 10000; //продолжительность асинхронного запроса 10 сек
	req.send(post); //отсылаем запрос
};

//событие при отправки формы
documentForm.submit(function(event){ 
 	SendRequest(); //формируем запрос и отправляем сообщение
 	documentForm.find("textarea").val(""); //очищаем поле ввода
 	return false; //отменяем перезагрузку страницы при отправления формы на сервер
});

//событие при смене статуса запроса отсылаем сообщение
req.onreadystatechange = function(){
	//Когда идет загрузка с сервера
	if (this.readyState != 4)
	{
		documentError.html("загрузка");
		return;
	}

	//Если обмен данными завершен и объект полностью инициализирован, выводим ошибки
	if (this.readyState == 4)
	{
	    if(this.status == 200) documentError.html(this.responseText); //Если код ответа от сервера 200
		else documentError.html("код ошибки: " + this.status + ' сообщение: ' + this.statusText + " сообщение не отправилось");
	}
}

//событие при возникновения ошибок
req.ontimeout = function() {
  documentError.html('Извините, запрос превысил максимальное время'); 
}

req.upload.onerror = function() {
	documentError.html('Произошла ошибка при загрузке данных на сервер!'); 
}


req.onerror = function(){
	documentError.html('Произошла ошибка при скачивания данных с сервер!'); 
}
<?php endif; ?>
/*========================================получения сообщений COMETE===============================================*/
<?php if(1): ?>
//------------------------------------myCOMETE (длинные опросы Long-polling)--------------------------------------//
<?php $_SESSION['top'] = $topic_id; //добавляем в сессию номер комнаты?>
var xhr1 = new XMLHttpRequest();

//функция для получение сообщений где формирует длинные опросы
var SendRequestCOMETE = function() {
	var url = "http://<?=$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']?>/comete-server/Long-polling/Long-polling.php"
	if (!this) return; //Проверяем существование запроса еще раз
	this.open('GET',url, true); //открываем соеденение и настраиваем асинхронный запрос
	this.send(); //отсылаем запрос
}

//событие при смене статуса запроса
xhr1.onreadystatechange = function(){
	//Когда идет загрузка с сервера
	if (this.readyState != 4) return;

	//Если обмен данными завершен и объект полностью инициализирован
	if (this.readyState == 4)
	{
	    if(this.status == 200) {handler(this.responseText); SendRequestCOMETE.call(xhr1);} //Если код ответа от сервера 200
		else SendRequestCOMETE.call(xhr1);//если это ошибка
	}
}
SendRequestCOMETE.call(xhr1); //отсылаем запрос сразу

<?php endif; ?>
<?php if(0): ?>
//--------------------------------------------------myCOMETE (WebSocket)----------------------------------------------//
var socket = new WebSocket("ws://<?=$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']?>/comete-server/WebSockets/WebSockets.php");

socket.onopen = function() {
  alert("Соединение установлено.");
  socket.send("Hello");

};

socket.onclose = function(event) {
  if (event.wasClean) alert('Соединение закрыто чисто');
  else alert('Обрыв соединения'); // например, "убит" процесс сервера
  alert('Код: ' + event.code + ' причина: ' + event.reason);
};

socket.onmessage = function(event) {
  alert("Получены данные " + event.data);
};

socket.onerror = function(error) {
  alert("Ошибка " + error.message);
};
<?php endif; ?>
/*=========================функция для обработки ответа от сервера=========================*/
var handler = function(text){
	chat.prepend(text); //добавляем сообщение в контейнер
	documentError.html(""); //обнуляем в случае удачи
};
</script>