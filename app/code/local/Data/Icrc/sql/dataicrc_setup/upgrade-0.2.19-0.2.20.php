<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
    
    // Réduire les attributs de recherche
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'origlang', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'orphan', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'page_number', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'price', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'product_type', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'short_description', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'status', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'target_audience', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id', 'is_searchable', false);
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'theme', 'is_searchable', false);
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
