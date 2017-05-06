<?php
//require_once "view/include.php";//включаем ядро сайта 
$seance = new mx\user_seance(); //инициализируем сессию
$marselDB = new mx\connectionDataBase($_SERVER['SERVER_NAME'],"marselDB","marsel","10041994"); //устанавливает соеденение с базой данных 