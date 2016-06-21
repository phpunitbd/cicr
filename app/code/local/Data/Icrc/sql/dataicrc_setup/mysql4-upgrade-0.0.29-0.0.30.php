<?php

$installer = $this;
$installer->startSetup();

// install status
$installer->run("
INSERT INTO `sales_order_status` (`status`, `label`) VALUES
('processing_partial_shipment', 'Partial Shipment')
");

// link it to state
$installer->run("
INSERT INTO `sales_order_status_state` (`status`, `state`, `is_default`) VALUES
('processing_partial_shipment', 'processing', 0)
");

$installer->endSetup();

