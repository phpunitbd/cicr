<?php

$installer = $this;
$installer->startSetup();

$installer->run("
INSERT INTO `order_attribute` 
(`title`, `content`, `status`, `created_time`, `update_time`) 
VALUES 
('Destination Type', '', '0', NOW(), NOW())
");

$installer->endSetup();

