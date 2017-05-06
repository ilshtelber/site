<?php
header("Content-type: image/jpg"); //заголовок для браузера HTTP-ответ
if(isset($_GET['topic']) && is_numeric($_GET['topic']))
{
	if($_GET['topic'] == 1) echo readfile(__DIR__.'/topic.jpg');
	if($_GET['topic'] == 2) echo readfile(__DIR__.'/policy.jpg');
	if($_GET['topic'] == 3) echo readfile(__DIR__.'/sport.jpg');
}
else 
{
	echo readfile(__DIR__.'/topic.jpg'); //
}