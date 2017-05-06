<?php #в этом файле мы подключаем ядро сайта
require_once str_replace("view",'',__DIR__)."core/function.php"; //ядро, там находятся функций для работы сайта 
spl_autoload_register(function($classname){require_once(str_replace("view",'',__DIR__).'core/class/'.str_replace("mx\\",'',$classname).'.php');}); //автозагрузка классов