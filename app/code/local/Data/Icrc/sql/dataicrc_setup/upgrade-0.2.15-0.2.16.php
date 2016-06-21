<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();
    // Création table pour sauvegarder les produits supprimés
    $installer->run("
    CREATE TABLE  `icrc_product_deleted` (
    `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `sku` VARCHAR( 64 ) NOT NULL ,
    `name` VARCHAR( 255 ) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
    `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Update Time',
    `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Deleted Time'
    ) ENGINE = INNODB COMMENT =  'Products deleted';
    ");
    
    $installer->endSetup();

} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
