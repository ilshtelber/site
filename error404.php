<!DOCTYPE html>
<html data-page="construction">
	<?php require_once "view/module/head.html";?>
	<style>
	@keyframes AnimatorBackground{from{background: black;} to { background: red; }}
	.error_header{color: red; font-weight: 600; font-size: 500%; text-align: center;}
	.error_wtf{font-size: 300%; text-align: center; margin: 50px }
	body{ animation: AnimatorBackground 1s ease-in-out .1s infinite alternate;}
	</style>
<body>
	<?php require_once "view/include.php";//включаем ядро сайта ?>
	<?php $seance = new mx\user_seance(); ?>
	<?php require_once "view/include.php"; ?>
	<?php require_once "view/module/header.php"; //шапка сайта?> 
	<section id="chatter"><div class="structure"> <div class="error_header">АШЫБКА 404</div> <div class="error_wtf">СПАСАЙТЕС КТО МОЖЕТ ОЛОЛО</div> <?php ?> </section>
	<?php require_once "view/module/footer.php";//подвал и глобальные скрипты?> 
</body>
</html>