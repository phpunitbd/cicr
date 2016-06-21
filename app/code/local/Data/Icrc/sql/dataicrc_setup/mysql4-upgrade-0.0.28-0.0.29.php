<?php

$int = Mage::getModel('core/store')->load('internal', 'code')->getId();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('currency/import/error_email', 'support@data.fr', 'default', 0);
$conf->saveConfig('currency/import/enabled', '1', 'default', 0);

$conf->saveConfig('design/head/default_title', "Internal ICRC Shop", 'stores', $int);
$conf->saveConfig('design/header/logo_alt', "Internal ICRC Shop", 'stores', $int);
$conf->saveConfig('design/header/welcome', "Internal Shop", 'stores', $int);
$conf->saveConfig('general/store_information/name', "Internal ICRC Shop", 'stores', $int);

$this->endSetup();


