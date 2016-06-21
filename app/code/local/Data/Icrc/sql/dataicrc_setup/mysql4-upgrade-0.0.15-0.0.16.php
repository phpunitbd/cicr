<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$en = Mage::getModel('core/store')->load('default', 'code')->getId();
$fr = Mage::getModel('core/store')->load('fr', 'code')->getId();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('customer/address/prefix_show', 'req', 'default', 0);
$conf->saveConfig('customer/address/prefix_options', ';Mr.;Mrs.;Dr.;Pr.', 'default', 0);
$conf->saveConfig('wishlist/general/active', '0', 'default', 0);
$setup->endSetup();

