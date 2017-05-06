<?php 
if(isset($_GET['top']) && $marselDB->isTopic($_GET['top']))
{

	$topic = $marselDB->outTopic(intval($_GET['top']));
	require_once("view/chatter.php");
}
else
{
	if(isset($_GET['top']) && is_numeric($_GET['top'])) echo '<div class="notification">Запрошенной темы не существует</div>';
	echo '<div><ul class="content-topic specialListStyle">';
	foreach($marselDB->outTopic() as $value) echo "<a href='/chat/$value[topic_id]'> <li class='clearfix'> <img src='/images/topic image/topic_image.php?topic=$value[topic_id]' alt=''> <div> <h3>$value[topic_name]</h3> <h5>$value[quantity] ".($value['quantity'] == 1?'сообщение':($value['quantity'] < 5?"сообщения":'сообщений'))."</h5> <span>$value[description]</span> </div> </li> </a>\n";

	echo '</div></ul>';
}
?>
