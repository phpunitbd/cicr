<?php

$installer = $this;
$installer->startSetup();

$installer->run("
INSERT INTO `order_attribute` 
(`order_attribute_id`, `title`, `content`, `status`, `created_time`, `update_time`) 
VALUES 
(2, 'Unit or Delegation', '', '1', NOW(), NOW()), 
(3, 'Cost Center', '', '0', NOW(), NOW()),
(4, 'Objective Code', '', '1', NOW(), NOW()), 
(5, 'Comment', '', '0', NOW(), NOW())
");

$installer->endSetup();

