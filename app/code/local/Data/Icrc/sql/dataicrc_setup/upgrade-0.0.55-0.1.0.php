<?php

$installer = $this;
$installer->startSetup();

list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('design/package/name', 'icrc', 'default', 0);
$conf->saveConfig('design/theme/default', 'default', 'default', 0);
$conf->saveConfig('design/theme/template', 'default', 'default', 0);
$conf->saveConfig('design/theme/skin', 'default', 'default', 0);
$conf->saveConfig('design/theme/layout', 'default', 'default', 0);
$conf->saveConfig('design/theme/template', 'internal', 'websites', $internal);

$installer->endSetup();

