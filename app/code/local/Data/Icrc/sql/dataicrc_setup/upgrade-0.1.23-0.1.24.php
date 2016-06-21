<?php

/**
* Sets prefix :
* - required for default website
* - not visible on internal website
*/
$this->startSetup();

$this->run("ALTER TABLE {$this->getTable('order_attribute_value')}
  CHANGE `value` `value` VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");
$this->run("ALTER TABLE {$this->getTable('order_attribute_defaults')}
  CHANGE `value` `value` VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''");

$this->endSetup();

