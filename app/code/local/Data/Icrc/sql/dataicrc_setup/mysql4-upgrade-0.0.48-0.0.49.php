<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('payment/free/active', '0', 'websites', $internal);

$this->run("update salesrule set 
  actions_serialized = replace(actions_serialized, 's:20:\"quote_item_row_total\"', 's:14:\"quote_item_qty\"')
  where is_active = 1 and name like 'Discount %-%'");

$this->endSetup();

