<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
    // Attribut pdf en type file
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'pdf', 
        array(
            'backend_model' => 'data_icrc/catalog_product_attribute_backend_file',
            'frontend_input' => 'file'
        )
    );

} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
