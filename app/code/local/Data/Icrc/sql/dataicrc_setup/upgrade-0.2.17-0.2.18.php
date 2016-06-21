<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();
    
    //Modificatoin message bienvenue store en
    $installer->setConfigData('icrc/web/submessage', 'of the International Committee of the Red Cross', 'default');
    
    $installer->endSetup();

} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
