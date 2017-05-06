<?php
namespace mx;

/**/
function f(){

}

/*генерируем токен для защиты от злых форм и атаки CSRF*/
function token($secret,$post_token = 0){
	if(!$secret) $secret = "тантум верде форте";
	if($post_token != 0)
	{
		$post_token = explode(":",$post_token,2);
		$salt = $post_token[0];
	}
	else
	{
		$salt = mt_rand(10000,99999); //соль, генерирует случайное число
	}
	return $salt.":".md5($salt.":".$secret);
}
/*============================================================================
		            функций для фильтраций внешних данных
============================================================================*/
/*фильтрует текстовое сообщение отправленнного ползователем, в случае неудачи возвращаем false*/
function filterMessage($post){
	$post = mb_strimwidth($post,0,10000); //обрезаем лишние символы, устанавливая при этом лимит
	$post = trim($post); //удаляем пробелы и ненужные символы "\t" "\n" "\r" "\0" "\x0B'"
	$post = addslashes($post); //экранируем, где добавляется на спецальные символы слэши
	#$post = htmlspecialchars($post,ENT_QUOTES); //преобразуем специальные символы в HTML сущность
	$post = filter_var($post,FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW); //фильтруем сообщение где преобразуем специальные символы в HTML сущность (то есть Экранирует HTML-символы '"<>&) и символы с ASCII-кодом, меньшим 32 (то же самое что и htmlspecialchars)
	$post = preg_replace("{((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)}im",'<a href="$1" target="_blank">$1</a>',$post); //заменяем внешние URL на HTTML эквивалент (ПЕРЕДЕЛАТЬ И СДЕЛАТЬ ПОЛУЧШЕ)
	$post = preg_replace(["{\-(\S.*\S)\-}im","{\*(\S.*\S)\*}im","{\+(\S.*\S)\+}im","{\_(\S.*\S)\_}im"],['<s>$1</s>','<b>$1</b>','<i>$1</i>','<u>$1</u>'],$post); //форматирование текста (-зачеркивание-) (*жирный*) (+курсив+) (_подчеркивание_)
	if($post == "" || $post == " ") return false; //если пользователь отправил пустое сообщение
	return $post;
}

/*фильтрует текст об пользователе (о себе), в случае неудачи возвращаем false*/
function filterMessageAbout($post){
	$post = mb_strimwidth($post,0,10000);
	$post = trim($post); 
	$post = addslashes($post); 
	$post = filter_var($post,FILTER_SANITIZE_SPECIAL_CHARS,FILTER_FLAG_STRIP_LOW); 
	$post = preg_replace("{((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)}im",'<a href="$1" target="_blank">$1</a>',$post); 
	if($post == "" || $post == " ") return false; 
	return $post;
}

/*фильтрует имя пользователя*/
function filterUser($post){
	$post = htmlspecialchars($post); //фильтруем
	$post = filter_var($post,FILTER_UNSAFE_RAW); //фильтруем, где удаляем или кодирует специальные символы
	return $post;
}

/*фильтрует пароль пользователя*/
function filterPassword($post){
	$post = filter_var($post,FILTER_SANITIZE_SPECIAL_CHARS); //фильтруем
	$post = filter_var($post,FILTER_UNSAFE_RAW); //фильтруем, где удаляем или кодирует специальные символы
	return $post;
}

/*фильтруем имя или фамилию*/
function filterSurnameName($post){
	$post = strip_tags($post); //удаляем ненужные нам теги и NULL-байты
	$post = filter_var($post,FILTER_UNSAFE_RAW); //фильтруем, где удаляем или кодирует специальные символы
	return $post;
}
/*============================================================================
		 функций для валидации внешних данных (проверка внешних данных)
============================================================================*/
/*приверяем на валидацию имя пользователя*/
function validateUser($postUser, &$message){
	#echo $postUser;
	if(!isset($postUser)) {$message = "ошибка"; return false;} //проверяем, существует ли такая переменная
	if(trim($postUser) == "" || trim($postUser) == " ") {$message = "вы не ввели имя пользователя"; return false;} //если имя пользоватея не заданно или задан только пробел
	#if(strip_tags($postUser) != $postUser) {$message = "нельзя использовать следующие символы"; return false;} //проверяем содержит ли имя тэги
	if(filter_var($postUser,FILTER_VALIDATE_REGEXP,['options'=>array('regexp'=>'{ \< | \> | \& | \# | \\ | \/ | \? | \! | \$ | \@ | \% | \* | \^ | \" | \' | \{ | \} | \( | \) }ixs')])) {$message = "нельзя использовать следующие символы  <  >  &  #  \\  /  ?  !  $  @  % * ^  \" { } ( )"; return false;} //недопускаем использования специальных символов
	if(mb_strlen($postUser) > 15) {$message = "слишком длиное имя пользователя"; return false;} //если символов у пользователя больше 15
	if(mb_strlen($postUser) < 3) {$message = "слишком короткое имя пользователя"; return false;}  //если мало символов

	return true;
}

/*приверяем на валидацию имя и фамилию пользователя*/
function validateSurnameName($postSurnameName, &$message){
	#echo $postSurnameName;
	if(!isset($postSurnameName)) {$message = "ошибка"; return false;} //проверяем, существует ли такая переменная
	if(trim($postSurnameName) == "" || trim($postSurnameName) == " ") {$message = "вы не ввели имя или фамилию"; return false;} //если имя пользоватея не заданно или задан только пробел
	if(filter_var($postSurnameName,FILTER_VALIDATE_REGEXP,['options'=>array('regexp'=>'{ \< | \> | \& | \# | \\ | \/ | \? | \! | \$ | \@ | \% | \* | \^ | \" | \' | \{ | \} | \( | \) }ixs')])) {$message = "нельзя использовать следующие символы  <  >  &  #  \\  /  ?  !  $  @  % * ^  \" { } ( )"; return false;} //недопускаем использования специальных символов
	if(mb_strlen($postSurnameName) > 15) {$message = "слишком длиное имя или фамилия"; return false;} //если символов у пользователя больше 15
	if(mb_strlen($postSurnameName) < 2) {$message = "слишком короткое имя или фамилия"; return false;}  //если мало символов
	return true;
}

/*приверяем на валидацию почтового ящика*/
function validateEmail($postEmail, &$message){
	if(!isset($postEmail)) {$message = "введите почтовый адресс"; return false;} 
	if(!filter_var($postEmail,FILTER_VALIDATE_EMAIL)) {$message="некорректный формат адреса электронной почты"; return false;}
	return true;
}

/*приверяем пароль пользователя*/
function validatePassword($postPassword, &$message){
	if(!isset($postPassword)){$message="введите пароль"; return false;} 
	if(strlen($postPassword) == 0){$message="введите пароль"; return false;}
	if(strlen($postPassword) < 3){$message="слишком простой пароль"; return false;}
	return true;
}

/*приверяем на валидацию файла (фотографию пользователя, те аватрку)*/
function validateFile($postFile, &$message){
	if(!isset($postFile)) return false; //проверяем, существует ли такая переменная

	if($postFile['error'] === 1) {$message = " Размер принятого файла превысил максимально допустимый размер"; return false;} //проверяем на ошибку
	if($postFile['error'] === 2) {$message = "слишком большой файл (max size 65Кб)"; return false;} //проверяем на ошибку
	if($postFile['error'] === 3) {$message = "Загружаемый файл был получен только частично"; return false;} //проверяем на ошибку
	//if($postFile['error'] === 4) {$message = "Отсутствует временная папка сервера, но это не точно"; return false;} //проверяем на ошибку

	if(!is_uploaded_file($postFile['tmp_name'])){$message = "файл не загружен"; return false;} //проферяем был ли загружен файл на сервер

	if($postFile['type'] != 'image/jpeg' && $postFile['type'] != 'image/png') {$message = "файл должен быть в формате png или jpg"; return false;} //исключяем файл, которые не удовлетворяют критерию касательно типа файла
	if($postFile['size'] > 65530) {$message = "слишком большой файл =("; return false;} //отсекаем если это слишком большой файл
	if($postFile['size'] < 100) {$message = "слишком маленький файл :("; return false;} //отсекаем если это слишком маленкий файл


	if($postFile['error'] != 0) {$message = "АШЫБКА"; return false;} //проверяем на ошибку ещё раз

	return true;
}
/*=================================================================
				функция для проверки запроса
===================================================================*/
/*проверяем POST данные, для добавления сообщений в БД*/
function verify_chat(){
	if(!isset($_POST['send']) && !isset($_POST['message']) && !isset($_POST['csrf'])) return false; //проверяем, существуют ли такие POST значении
	if(!$_POST['send'] == "yes") return false; //проверяем, переденно ли было скрипту запрос POST со значением send = yes
	if(!$_POST['csrf'] == token(false,$_POST['csrf'])) return false; //проверяем на токен
	return true; //успех
}

/*проверяем POST данные, для добавления пользователя в БД*/
function verify_user(){
	if(!isset($_POST['send']) && !isset($_POST['user']) &&  !isset($_POST['pass']) && !isset($_POST['csrf'])) return false; //проверяем, существуют ли такие POST значении
	if(!$_POST['send'] == "yes") return false; //проверяем, переденно ли было скрипту запрос POST со значением send = yes
	if(!$_POST['csrf'] == token(false,$_POST['csrf'])) return false; //проверяем на токен
	return true; //успех
}