<?php #в этом файле хранится подвал, а так же глобальные скрипты для сайта ?>
<!--<script src="//parking.jino.ru/static/main.js" charset="utf-8"></script>-->
<footer class="structure clearfix primary-footer"> 
	<span><a href="https://vk.com/khisamutdin0v">&#10054; Марсель Хсамутдинов</a></span>
	<nav>
    	<ul>
	    	<li><a href="/chat">чат</a></li>
	    	<li><a href="/user">пользователи</a></li>
	    	<li><a href="/<?=$seance->Authentication()?"about":"register"?>"><?=$seance->Authentication()?"моя страница":"регистрация"?></a></li>
    	</ul>
    </nav>
</footer>
