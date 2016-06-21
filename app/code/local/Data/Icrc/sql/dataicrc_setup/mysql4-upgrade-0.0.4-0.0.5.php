<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('web/default/front', "icrc/home", 'default', 0);
$conf->saveConfig('design/theme/template', "icrc", 'default', 0);
$conf->saveConfig('design/theme/skin', "icrc", 'default', 0);
$conf->saveConfig('design/theme/layout', "icrc", 'default', 0);
$conf->saveConfig('design/admin/theme', "icrc", 'default', 0);

$setup->endSetup();

