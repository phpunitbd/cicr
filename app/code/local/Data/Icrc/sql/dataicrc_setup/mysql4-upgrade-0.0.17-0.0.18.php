<?php

$installer = $this;
$installer->startSetup();

$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('order_attribute_label')};
CREATE TABLE {$this->getTable('order_attribute_label')} (
  `order_attribute_label_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `store_view` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`order_attribute_label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO  `order_attribute_label` (`code`, `label`, `store_view`)
VALUES
('ngo', 'NGO', '0'), ('ngo', 'NGO', '$en'), ('ngo', 'ONG', '$fr'),
('', '', '0'), ('', '', '$en'), ('', '', '$fr'),
('library', 'Library', '0'), ('library', 'Library', '$en'), ('library', 'Bibliothèque', '$fr');
");

$installer->endSetup();
