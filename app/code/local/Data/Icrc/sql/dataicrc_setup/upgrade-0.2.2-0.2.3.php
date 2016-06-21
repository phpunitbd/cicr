<?php

$this->startSetup();

$this->run("
CREATE TABLE  `icrc_customer_billing_info` (
`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`customer_id` VARCHAR( 255 ) NOT NULL ,
`unit` VARCHAR( 255 ) NOT NULL ,
`cost_center` VARCHAR( 255 ) NOT NULL ,
`objective_code` VARCHAR( 255 ) NOT NULL
) ENGINE = INNODB COMMENT =  'Customers billing info presets';
");

$this->endSetup();

