<?php

$installer = $this;
$installer->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('shipping/origin/country_id', 'CH', 'default', 0);
$conf->saveConfig('shipping/origin/region_id', null, 'default', 0);
$conf->saveConfig('shipping/origin/postcode', '1202', 'default', 0);
$conf->saveConfig('shipping/origin/city', 'Geneva', 'default', 0);
$conf->saveConfig('shipping/origin/street_line1', '19 Avenue de la paix', 'default', 0);
$conf->saveConfig('shipping/origin/street_line2', null, 'default', 0);

$installer->endSetup();

