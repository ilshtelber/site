<?php
namespace mx;

/*данный класс управляет сеансом и прибывания на сайте пользоватля*/
class user_seance
{
	private $user = "";
	private $password = "";

	/**/
	public function __construct()
	{
		if(!isset($_SESSION)) session_start();
		if(isset($_SESSION["seance"]))
		{
			$this->user = $_SESSION["seance"]["user"];
			$this->password = $_SESSION["seance"]["pass"];
		}
	}

	/*Идентификация пользователя*/
	public function identification($new_user,$new_password)
	{
		$_SESSION["seance"]["user"] = $new_user;
		$_SESSION["seance"]["pass"] = $new_password;
		self::__construct();
	}

	/*аунтедификация пользователя, проверка подлинности пользователя, если пользователь аутентифицирован то вернет статус пользователя, иначе false*/
	public function Authentication()
	{
		if(isset($_SESSION["seance"]) && $this->user != "" && $this->password != "")
		{
			try
			{
				$db = new connectionDataBase($_SERVER['SERVER_NAME'],"marselDB","marsel","10041994"); //открываем базу данных
				$status = $db->query("SELECT * FROM user WHERE user = '$this->user'")->fetch()['status']; //извлекаем статус пользователя
				if($db->isUser($this->user,$this->password)) return $status; //проверяем пользователя совместимость на пароль
			}
			catch (PDOException $e)
			{
				return 'usual';
			}
		}
		return false;
	}

	/*выход пользователя из сенаса*/
	public function exit()
	{
		unset($_SESSION["seance"]);
	}

	/*пользователь*/
	public function user()
	{
		return $this->user;
	}

	//пароль рользователя
	public function password()
	{
		return $this->password;
	}
}