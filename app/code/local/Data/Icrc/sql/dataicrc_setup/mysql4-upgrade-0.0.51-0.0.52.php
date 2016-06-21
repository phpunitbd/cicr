<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('persistent/options/enabled', '1', 'default', 0);
$conf->saveConfig('persistent/options/remember_enabled', '1', 'default', 0);

$this->endSetup();

