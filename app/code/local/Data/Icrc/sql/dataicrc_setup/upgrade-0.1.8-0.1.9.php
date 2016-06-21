<?php

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE  `icrc_sku_mapping` (
`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`magento_sku` VARCHAR( 255 ) NOT NULL ,
`antalis_sku` VARCHAR( 50 ) NOT NULL
) ENGINE = INNODB COMMENT =  'Magento-Antalis SKU mapping table';
");

$installer->endSetup();

