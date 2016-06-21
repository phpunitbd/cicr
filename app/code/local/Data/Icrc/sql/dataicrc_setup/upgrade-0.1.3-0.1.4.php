<?php

$installer = $this;
$installer->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$address = "International Committee of the Red Cross
19 Avenue de la paix CH 1202 Geneva
Switzerland";

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('sales/identity/address', $address, 'default', 0);
$conf->saveConfig('sales/identity/logo', 'stores/fr/CICR.png', 'stores', $fr);
$conf->saveConfig('sales/identity/logo', 'default/ICRC.png', 'default', 0);

$installer->endSetup();

