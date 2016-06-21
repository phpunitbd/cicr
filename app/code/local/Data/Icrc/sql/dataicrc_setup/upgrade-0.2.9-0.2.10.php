<?php

$this->startSetup();

$sql = "CREATE TABLE IF NOT EXISTS `radar_acronym` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `french_name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$this->run($sql);
$sql = "CREATE TABLE IF NOT EXISTS `radar_delegation` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `main_site_name` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$this->run($sql);
$sql = "CREATE TABLE IF NOT EXISTS `radar_costcenter` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$this->run($sql);
$sql = "CREATE TABLE IF NOT EXISTS `radar_objective` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_ocode` varchar(30) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
$this->run($sql);

$this->endSetup();

