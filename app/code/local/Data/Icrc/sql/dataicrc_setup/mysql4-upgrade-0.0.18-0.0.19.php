<?php

$installer = $this;
$installer->startSetup();

$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$installer->run("
INSERT INTO  `order_attribute_label` (`code`, `label`, `store_view`)
VALUES
('public', 'General public', '0'), ('public', 'General public', '$en'), ('public', 'Grand public', '$fr'),
('museum', 'Museum', '0'), ('museum', 'Museum', '$en'), ('museum', 'Musée', '$fr'),
('federation', 'Federation', '0'), ('federation', 'Federation', '$en'), ('federation', 'Fédération', '$fr'),
('society', 'National Society', '0'), ('society', 'National Society', '$en'), ('society', 'Société nationale', '$fr'),
('int', 'International Organization', '0'), ('int', 'International Organization', '$en'), ('int', 'Organisation internationale', '$fr'),
('gov', 'Government', '0'), ('gov', 'Government', '$en'), ('gov', 'Gouvernement', '$fr'),
('academic', 'Academic', '0'), ('academic', 'Academic', '$en'), ('academic', 'Universitaire', '$fr'),
('education', 'School', '0'), ('education', 'School', '$en'), ('education', 'Éducation', '$fr'),
('bookshop', 'Bookshop', '0'), ('bookshop', 'Bookshop', '$en'), ('bookshop', 'Librairie', '$fr'),
('healthcare', 'Health Professional', '0'), ('healthcare', 'Health Professional', '$en'), ('healthcare', 'Professionnel de santé', '$fr'),
('media', 'Media', '0'), ('media', 'Media', '$en'), ('media', 'Media', '$fr'),
('law', 'Law Professional', '0'), ('law', 'Law Professional', '$en'), ('law', 'Métiers du droit', '$fr');
");

$installer->run("UPDATE `order_attribute` 
SET `content` = 'public,ngo,library,museum,federation,society,int,gov,academic,education,bookshop,healthcare,media,law'
WHERE `order_attribute`.`order_attribute_id` = 1;");

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('currency/options/base', 'CHF', 'default', 0);
$conf->saveConfig('currency/options/default', 'CHF', 'default', 0);
$conf->saveConfig('currency/options/allow', 'USD,EUR,CHF', 'default', 0);
$conf->saveConfig('web/session/use_frontend_sid', '0', 'default', 0);
$conf->saveConfig('api/config/compliance_wsi', '1', 'default', 0);
$conf->saveConfig('api/config/wsdl_cache_enabled', '1', 'default', 0);
$conf->saveConfig('catalog/frontend/list_mode', 'list-grid', 'default', 0);
$conf->saveConfig('catalog/frontend/grid_per_page_values', '4,6,8,12,24', 'default', 0);
$conf->saveConfig('catalog/frontend/grid_per_page', '4', 'default', 0);
$conf->saveConfig('catalog/frontend/list_per_page_values', '5,10,15,20,25', 'default', 0);
$conf->saveConfig('catalog/frontend/list_per_page', '5', 'default', 0);
$conf->saveConfig('catalog/frontend/list_allow_all', '1', 'default', 0);
$conf->saveConfig('catalog/frontend/flat_catalog_category', '1', 'default', 0);
$conf->saveConfig('catalog/frontend/flat_catalog_product', '1', 'default', 0);
$conf->saveConfig('cataloginventory/options/show_out_of_stock', '1', 'default', 0);
$conf->saveConfig('customer/create_account/confirm', '1', 'default', 0);
$conf->saveConfig('payment/ccsave/active', '0', 'default', 0);
$conf->saveConfig('payment/checkmo/active', '0', 'default', 0);
$conf->saveConfig('payment/saferpay_creditcard/active', '1', 'default', 0);
$conf->saveConfig('payment/paypal_billing_agreement/active', '0', 'default', 0);
$conf->saveConfig('payment/free/active', '1', 'default', 0);
$conf->saveConfig('payment/free/order_status', 'processing', 'default', 0);
$conf->saveConfig('saferpay/settings/http_client_adapter', 'Zend_Http_Client_Adapter_Curl', 'default', 0);

//$conf->saveConfig('', '0', 'default', 0);

$installer->endSetup();
