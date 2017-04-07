<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
//状态 1 推单进行中 、2推单暂停 、0推单结束
//start to put your own code DROP TABLE IF EXISTS `pre_jubei_task_list`;
$sql = <<<EOF
drop table IF EXISTS `pre_jubei_task_list`;
CREATE TABLE IF NOT EXISTS `pre_jubei_task_list` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(32) NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  `pingtai_name` varchar(32) NOT NULL default '',
  `list` text,
  `taskremark` text,
  `begintime` varchar(32) NOT NULL default '',
  `endtime` varchar(32) NOT NULL default '',
  `status` varchar(32) NOT NULL default '',
  `islog` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
drop table IF EXISTS `pre_jubei_task_get`;
CREATE TABLE IF NOT EXISTS `pre_jubei_task_get` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `taskid` int(10) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `qq` varchar(32) NOT NULL default '',
  `username` varchar(32) NOT NULL default '',
  `money` int(10) unsigned NOT NULL default '0',
  `num` int(10) unsigned NOT NULL default '0',
  `begintime` varchar(32) NOT NULL default '',
  `endtime` varchar(32) NOT NULL default '',
  `status` varchar(32) NOT NULL default '',
  `islog` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
drop table IF EXISTS `pre_jubei_task_complete`;
CREATE TABLE IF NOT EXISTS `pre_jubei_task_complete` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `taskid` int(10) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(255) NOT NULL,
  `tel` varchar(16) NOT NULL,
  `name` varchar(32) NOT NULL,
  `qq` varchar(16) NOT NULL,    
  `zfb` varchar(32) NOT NULL,
  `money` varchar(255) NOT NULL,
  `begintime` varchar(32) NOT NULL default '',
  `other` text,
  `other1` varchar(255) NOT NULL default '',
  `other2` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`)
);

drop table IF EXISTS `pre_jubei_task_message`;
CREATE TABLE IF NOT EXISTS `pre_jubei_task_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(255) NOT NULL,
  `qq` varchar(16) NOT NULL,    
  `zfb` varchar(32) NOT NULL,

  PRIMARY KEY (`id`)
);

EOF;

runquery($sql);
//finish to put your own code DROP TABLE IF EXISTS `pre_alj_fingertask_log`;
$finish = TRUE;
?>