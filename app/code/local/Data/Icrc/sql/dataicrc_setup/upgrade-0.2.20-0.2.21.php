<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
    
    // Réduire les attributs de recherche
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'contentdate', 'used_for_sort_by', true);
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
