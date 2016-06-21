<?php

$installer = $this;
$installer->startSetup();

// install statuses
$installer->run("
INSERT INTO `sales_order_status` (`status`, `label`) VALUES
('processing_sent_to_logistic', 'Sent to Antalis'),
('processing_validated', 'Validated')
");

// link them to states
$installer->run("
INSERT INTO `sales_order_status_state` (`status`, `state`, `is_default`) VALUES
('processing_sent_to_logistic', 'processing', 0),
('processing_validated', 'processing', 0)
");

// Rename "Processing"
$installer->run("
UPDATE `sales_order_status` set `label` = 'To Validate'
WHERE `label` = 'Processing'
");

$installer->endSetup();

