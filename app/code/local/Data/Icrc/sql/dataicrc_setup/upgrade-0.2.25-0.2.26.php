<?php
try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'description', 'is_searchable', true);
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}