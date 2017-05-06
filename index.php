<?php require_once "view/include.php";//включаем ядро сайта ?>
<?php require_once "view/declare.php";//объявляем классы ?>
<?php if(isset($_GET['mx']) && $_GET['mx'] == "chat") $mx = 'view/section_topics.php'; //страница чата?>
<?php if(isset($_GET['mx']) && $_GET['mx'] == "user") $mx = 'view/section_user.php'; //страница пользователей?>
<?php if(isset($_GET['mx']) && $_GET['mx'] == "register") $mx = 'view/section_register.php'; //страница регистраций?>
<?php if(isset($_GET['mx']) && $_GET['mx'] == "about") $mx = 'view/section_about.php'; //страница пользователя?>
<!DOCTYPE html>
<html data-page="construction">
	<?php require_once "view/module/head.html";?>
<body>
	<?php require_once "view/module/header.php"; //шапка сайта?> 
	<?php require_once "view/module/section.php"; //секция, зависема от переменной $mx?> 
	<?php require_once "view/module/footer.php";//подвал и глобальные скрипты?> 
</body>
</html>
<?php session_write_close(); ?>