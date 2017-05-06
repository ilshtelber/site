<?php
namespace mx;
/**/
class connectionDataBase extends \PDO
{
	/*устанавливает соеденение с базой данных и создает таблицу chatter,user если их нет.*/
	public function __construct($host,$dbname,$login,$password)
	{
		try
		{
			parent::__construct("mysql:host=$host; dbname=$dbname",$login,$password); //неявно вызываем конструтор класса PDO, где устанавливаем соеденения с БД
			$flag = [true,true,true,true]; //флаг для проверки
			$table = $this->query("SHOW TABLES"); //выводит все таблицы хранящиеся в БД
		
			while($i = $table->fetch()){
				if($i[0] == "user") $flag[0] = false; //проверяем, есть ли в БД таблица user
				if($i[0] == "topics") $flag[1] = false; //проверяем, есть ли в БД таблица Topics
				if($i[0] == "chatter") $flag[2] = false; //проверяем, есть ли в БД таблица chatter
			}

			//cоздаем ВРМЕННУЮ таблицу story(где хранятся мой рассказы или посты)
			if(false) $this->exec("CREATE TABLE story(story_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, addDate DATE NOT NULL, addText TEXT, addCaption VARCHAR(64) NOT NULL)");

			//cоздаем таблицу user(где хранятся данные пользователей), если его не существует
			if($flag[0]) $this->exec("CREATE TABLE user(
			user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, #id пользователя
			user VARCHAR(16) NOT NULL UNIQUE, #имя пользователя, должен быть уникальным (только на латинском)
			name VARCHAR(32) NOT NULL, #имя пользователя
			surname VARCHAR(32) NOT NULL, #фамиля пользователя
			about TEXT, #о себе
			sex ENUM('female','male'), #пол пользователя 
			status ENUM('unconfirmed','usual','moderator','admin'), #статус пользователя, которые определяет привелегий для пользователя
			contacts SET(''), #здесь хранятся другие пользователи котороые входят в друзья (контакты) пользователя (не реализ)
			avatar_type VARCHAR(32), #тип файла у аватарки (jpeg,png)
			avatar BLOB, #аватарка, хранит картинку в бинарном режиме
			state INT(11), #состояние пользователя, онлайн (если 1 и т.д) или офлайн (если 0)
			registration_date DATE NOT NULL, #дата создания пользователя
			mail VARCHAR(32) NOT NULL, #почта e-mail пользователя
			password VARCHAR(255) NOT NULL #пароль пользователя
			)");

			//cоздаем таблицу Topics (где хранятся комнаты для чата), если его не существует
			if($flag[1]) $this->exec("CREATE TABLE topics(
			topic_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, #id темы
			quantity INT(11) NOT NULL, #количество сообщений
			topic_name VARCHAR(64) NOT NULL UNIQUE, #название темы комнаты, должен быть уникальным
			description TEXT, #описание темы, о чем эта тема
			picture BLOB, #здесь будет храниться изображения темы
			Access_key VARCHAR(32), #ключ для доступа к запороленному темы комнаты
			mode ENUM('all', 'some', 'key') #режим доступа таблицы (all - доступен всем, some - только зарегестрированным пользователям, key - запороленная тема, вход в комнату только через ключ)
			)");

			//cоздаем таблицу chatter (где хранятся сообщения пользователей), если его не существует
			if($flag[2]) $this->exec("CREATE TABLE chatter(
			message_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, #id сообщений
			messageTopic_id INT(11) NOT NULL, #id сообщений по топику
			adddate DATETIME NOT NULL, #дата отправки сообщений
			addtext TEXT, #текст сообщений
			putlike INT UNSIGNED DEFAULT 0, #кол-во лайков от пользователей
			putdislike INT UNSIGNED DEFAULT 0, #кол-во дизлайков от пользователей
			user_id INT(11) NOT NULL, #id пользователя оставившего сообщений, по вторичному ключу
			topic_id INT(11) NOT NULL, #id форума или комнаты или тема которому прнадлежит сообщение, по вторичному ключу
			FOREIGN KEY(user_id) REFERENCES user(user_id), FOREIGN KEY(topic_id) REFERENCES topics(topic_id))");

			//создаем таблицу private_chat (где хранятся личные, приватные, сообщения, между пользователем), если его не существует
			#if($flag[3]) $this->exec("CREATE TABLE private_chat()"); НЕ РЕАЛИЗОВАН РЕАЛИЗОВАТЬ В БУДУЩЕМ
		}
		catch (PDOException $e)
		{
			echo "невозможно установить соеденение с базой данных маза фака, причина: ".$e->getMessage();
			throw 1;
		}
	}

	/*если такого пользователя не существует, то метод добавит нового пользователя в базу данных и возвращает true, иначе, в случае неудачи или пользователь есть в базе данных, возвращает false, так же хэширует пароль пользователя*/
	public function addUser($user,$name,$surname,$about,int $sex,int $status,$mail,$password)
	{
		$state = false; //статус

		switch($sex)
		{
			case 1: $sex = "female"; break;
			case 2: $sex = "male"; break;
			default: return false;
		}

		switch($status)
		{
			case 1: $status = "unconfirmed"; break;
			case 2: $status = "usual"; break;
			case 3: $status = "moderator"; break;
			case 4: $status = "admin"; break;
			default: return false;
		}

		$password = password_hash($password, PASSWORD_BCRYPT); //хэшируем пароль
		$isUser = $this->query("SELECT EXISTS(SELECT * FROM user WHERE user = '$user')")->fetch()[0]; //проверяем, есть ли в базе данных выбранный пользователь (если есть возвращает true иначе false)
		$isEmail = $this->query("SELECT EXISTS(SELECT * FROM user WHERE mail = '$mail')")->fetch()[0]; //проверяем, есть ли в базе данных такой-же email пользователя(если есть возвращает true иначе false)
		if(!$isUser && !$isEmail) $state = $this->exec("INSERT INTO user VALUES(NULL,'$user','$name','$surname','$about','$sex','$status',NULL,NULL,NULL,0,NOW(),'$mail','$password')"); //добавляем пользователя в базу данных, если его нет в базе данных и похожего почтового адреса иначе возвращаем false
		if($isUser || !$state) return false; //иначе если пользователь есть в базе данных или пользователь не добавился в БД, то возвращаем false
		return true; //если пользователь был добавлен в базу данных, то возвращаем true
	}

	/*обновляет данные в таблице у пользователя, через его имя и пароль, в случе успеха возвращаем true, иначе false*/
	public function updateUser($user,$password,$column,$value)
	{
		$user_inform = $this->query("SELECT user_id, password FROM user WHERE user='$user'")->fetch(); //извлекаем пароль и id пользователя
		if(!password_verify($password,$user_inform['password'])) return false; //если пароль не совпалось
		echo $user;
		//обновляем информацию о себе
		if($column === 'about')
		{
			$state = $this->exec("UPDATE user SET $column = '$value' WHERE user = '$user' AND user_id = $user_inform[user_id]");
			return $state;
		}

		//обновляем имя пользователя
		if($column === 'name')
		{
			$state = $this->exec("UPDATE user SET $column = '$value' WHERE user = '$user' AND user_id = $user_inform[user_id]");
			return $state;
		}

		//обновляем фамилию пользователя
		if($column === 'surname')
		{
			$state = $this->exec("UPDATE user SET $column = '$value' WHERE user = '$user' AND user_id = $user_inform[user_id]");
			return $state;
		}

		//обновляем фотографию пользовотеля
		if($column === 'avatar')
		{
			$bin = addslashes(file_get_contents($value['tmp_name'],0)); //извлекаем файл и экрнаируем опасные символы
			$type = addslashes($value['type']);
			$state = $this->exec("UPDATE user SET $column = '$bin', avatar_type = '$type' WHERE user = '$user' AND user_id = $user_inform[user_id]");
			echo !$state;
			return $state;
		}
		return false; //вслучае если передали не столбец а хуйню
		
	}
	/*выводит пользователя по id или всех пользователей*/
	public function outUser($id = 0)
	{
		if(!is_numeric($id)) return false; //если решил наебнуть систему, то есть передать хрень не являющеимся числом
		if($id != 0) 
		{
			$entry = $this->query("SELECT * FROM user WHERE  user_id = $id"); //возвращает пользователя по id
			return $entry->fetch(); //возвращаем результат
		} 
		else
		{
			$entry = $this->query("SELECT * FROM user"); //возвращает всех пользователей
			return $entry->fetchall(); //возвращаем результат
		}
	}

	//проверяем, есть ли такой пользователь в Базе данных, и совпадает у него пароль, в случае удачи возвращаем true иначе false
	public function isUser($user,$password)
	{
		if((trim($user) === "" || trim($user) == " ") && trim($password) == "") return false; //если был введн некоректный пользователь или пароль
		$user_inform = $this->query("SELECT password FROM user WHERE user='$user'")->fetch(); //делаем запрос на id пользователя и его пароль
		if(!$user_inform) return false; //если пользователь не был найден
		if(!password_verify($password,$user_inform['password'])) return false; //проверяем, совпадает ли пароль из БД
		return true;
	}

	/*добавляет сообщение и возвращает пустую строку, иначе, в случае неудачи, возвращает текст, который гласит, что за ошибка произошла, во время добавления сообщений в базу данных*/
	public function addMessage($user,$password,$message,$topic_id)
	{
		if(!$message) return "сообщение не принято, введите текст"; //если сообщение пустое
		$user_inform = $this->query("SELECT user_id, password FROM user WHERE user='$user'")->fetch(); //делаем запрос на id пользователя и его пароль
		if(!$user_inform) return "вы не авторизированны"; //если пользователь не найден

		$col = $this->query("SELECT count(*) FROM chatter WHERE topic_id = $topic_id")->fetch()[0] + 1; //кол-во сообщений топика
		if(is_numeric($user_inform[0]) && password_verify($password,$user_inform['password'])) $status = $this->exec("INSERT INTO chatter VALUES(NULL,$col,NOW(),'$message',DEFAULT,DEFAULT,$user_inform[user_id],$topic_id)"); //добавляем сообщение в таблицу если пользователь найден в базе данных

		//в случае ошибок
		if(!$status) return "запрос не выполнен";
		if(!password_verify($password,$user_inform['password'])) return "вы не авторизированны (неверный пароль)"; //проверка на пароль

		$this->exec("UPDATE topics SET quantity = $col WHERE topic_id = $topic_id"); //обновляем кол-во сообщений у топиков

		return ""; //в случае успеха, те пользователь был найден, то возвращаем пустую строку
	}

	/*данный метод выводит все сообщении пользователей c указанным номером топика*/
	public function outMessage(int $topic_id)
	{
		$table = $this->query("SELECT user.user_id, user.user, chatter.messageTopic_id, chatter.adddate, chatter.addtext FROM user, chatter WHERE chatter.user_id = user.user_id AND chatter.topic_id = $topic_id ORDER BY chatter.message_id DESC"); //выполняем запрос SQL, где извлекаем данных из таблицы, и ввыодим сообщений пользователей
		while($value = $table->fetch()) yield $value; //выводим данные, то есть сообщений пользователей через итератор при помощи генератора
		#while($value = $table->fetch()) $arr[] = $value; return $arr; //выводим данные, то есть сообщений пользователей, через массивы (запас)
	}

	/**/
	public function isMessage()
	{
		return false;
	}

	/*данный метод добавит новую комнату или тему форума для сообщений*/
	public function addTopic($topic_name,$description,$picture,$Access_key,$mode)
	{
		$picture = $picture?"'$picture'":"NULL"; //если изображения темы отсуствует, то будет NULL
		$Access_key = $Access_key?"'$Access_key'":"NULL"; //если ключь доступа равен flase, то ключь доступа будет равен NULL
		if($this->query("SELECT EXISTS(SELECT * FROM topics WHERE topic_name = '$topic_name')")->fetch()[0]) return false;

		//режим для темы чата
		switch($mode)
		{
			case 1: $mode = "all"; break;
			case 2: $mode = "some"; break;
			case 3: $mode = "key"; break;
			default: return false;
		}

		//добавляем тему для чата в базу данных
		$status = $this->exec("INSERT INTO topics VALUES(NULL,0,'$topic_name','$description',$picture,$Access_key,'$mode')");
		//проверки после добавления темы
		if(!$status) return false;
		else return true;
	}

	/*Данный метод вводит только одну или все темы форума*/
	public function outTopic(int $id = 0)
	{
		if($id != 0)
		{
			return $this->query("SELECT * FROM topics WHERE topic_id = $id")->fetch(); //возвращает пользователя по id (не работает, так как это функция итератор)
		}
		$table = $this->query("SELECT * FROM topics"); //выполняем запрос SQL, где все извлекаем топики
		while($value = $table->fetch()) yield $value; //выводим все топики через итератор
	}

	/*проверяет, существет ли такой топик вообще, в случае удачи возвращает true*/
	public function isTopic($topic_id)
	{
		if(!is_numeric($topic_id))  return false;
		if(!$this->query("SELECT EXISTS(SELECT * FROM topics WHERE topic_id = '$topic_id')")->fetch()[0]) return false;
		return true;
	}
}