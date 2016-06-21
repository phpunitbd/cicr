<?php

$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('order_attribute')};
CREATE TABLE {$this->getTable('order_attribute')} (
  `order_attribute_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`order_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO  `order_attribute` (`title`, `content`, `status`, `created_time`, `update_time`)
VALUES ('Customer Type', 'ngo,library', '1', NOW() , NOW());
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('order_attribute_value')};
CREATE TABLE {$this->getTable('order_attribute_value')} (
  `order_attribute_value_id` int(11) unsigned NOT NULL auto_increment,
  `order_attribute_id` int(11) unsigned NOT NULL,
  `quote_id` int(11) unsigned NOT NULL,
  `value` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`order_attribute_value_id`),
  UNIQUE KEY `order_attribute` (`order_attribute_id`,`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('order_attribute_defaults')};
CREATE TABLE {$this->getTable('order_attribute_defaults')} (
  `order_attribute_defaults_id` int(11) unsigned NOT NULL auto_increment,
  `order_attribute_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`order_attribute_defaults_id`),
  UNIQUE KEY `order_attribute` (`order_attribute_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
