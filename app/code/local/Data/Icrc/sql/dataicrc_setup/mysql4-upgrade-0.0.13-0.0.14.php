<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('design/header/logo_src', 'images/Logo.png', 'default', 0);
$conf->saveConfig('design/footer/copyright', '&copy; 2012 ICRC', 'default', 0);
$conf->saveConfig('design/footer/copyright', '&copy; 2012 CICR', 'stores', $fr);

$setup->endSetup();

