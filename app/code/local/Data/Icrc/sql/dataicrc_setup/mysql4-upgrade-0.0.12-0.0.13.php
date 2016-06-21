<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('carriers/icrc/active', '1', 'default', 0);
$conf->saveConfig('carriers/flatrate/active', '0', 'default', 0);
$conf->saveConfig('carriers/tablerate/active', '0', 'default', 0);
$conf->saveConfig('carriers/freeshipping/active', '0', 'default', 0);
$conf->saveConfig('carriers/ups/active', '0', 'default', 0);

$setup->endSetup();

