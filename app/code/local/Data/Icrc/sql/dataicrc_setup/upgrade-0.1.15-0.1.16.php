<?php

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE  `icrc_datastudio_log` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` int(10) NOT NULL,
  `status` varchar(50) NOT NULL,
  `message` varchar(1024) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE = INNODB COMMENT =  'Log for Datastudio';
");

$installer->endSetup();

