<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$conf = new Mage_Core_Model_Config();
//$conf->saveConfig('web/default/front', 'icrc/home', 'default', 0);
$conf->saveConfig('general/locale/code', 'en_US', 'default', 0);
$conf->saveConfig('general/locale/code', 'en_US', 'stores', $en);
$conf->saveConfig('general/locale/code', 'fr_FR', 'stores', $fr);
$conf->saveConfig('general/locale/firstday', '1', 'default', 0);

$setup->endSetup();

