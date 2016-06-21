<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();
    // Création table pour sauvegarder les produits supprimés
    $installer->run("
    ALTER TABLE  `icrc_product_deleted` 
    ADD `store_id` INT( 10 ) NOT NULL");
    
    $installer->endSetup();

} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
