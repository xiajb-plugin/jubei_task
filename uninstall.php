<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
drop table IF EXISTS `pre_jubei_task_list`;
drop table IF EXISTS `pre_jubei_task_complete`;
EOF;
runquery($sql);
$finish = TRUE;
?>