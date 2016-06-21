<?php
$this->startSetup();

$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();
$en = Mage::getModel('core/store')->load('default', 'code')->getId();

$conf = new Mage_Core_Model_Config();

$conf->saveConfig('design/head/default_title', "ICRC Shop", 'default', 0);
$conf->saveConfig('design/header/logo_alt', "ICRC Shop", 'default', 0);
$conf->saveConfig('design/header/welcome', "Shop", 'default', 0);
$conf->saveConfig('general/store_information/name', "ICRC Shop", 'default', 0);

$conf->saveConfig('design/head/default_title', "ICRC Shop", 'stores', $en);
$conf->saveConfig('design/header/logo_alt', "ICRC Shop", 'stores', $en);
$conf->saveConfig('design/header/welcome', "Shop", 'stores', $en);
$conf->saveConfig('general/store_information/name', "ICRC Shop", 'stores', $en);

$conf->saveConfig('design/header/logo_alt', "Boutique CICR", 'stores', $fr);
$conf->saveConfig('design/head/default_title', "Boutique CICR", 'stores', $fr);
$conf->saveConfig('design/header/welcome', "Boutique", 'stores', $fr);
$conf->saveConfig('general/store_information/name', "Boutique CICR", 'stores', $fr);

$this->endSetup();

