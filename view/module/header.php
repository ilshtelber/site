<?php #в этом файле хранится шапка сайта 
	$available = $seance->Authentication(); //доступен ли вход
	$title = $seance->Authentication()?"моя страница":"регистрация";
	$link = $seance->Authentication()?"about":"register";
?>
<header class="structure clearfix primary-header">
    <nav>
    	<ul>
    	<li><a href="/index.php"><img src="\images\logo\глонасс.png" alt=""></a></li>
    	<li><a href="/chat">чат</a></li>
    	<li><a href="/user">пользователи</a></li>
    	<li><a href="/<?=$link?>"><?=$title?></a></li>
    	</ul>
    </nav>
</header>