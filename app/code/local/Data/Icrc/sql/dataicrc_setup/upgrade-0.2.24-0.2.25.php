<?php
try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
    
    $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
        ->addFieldToFilter('is_searchable', 1);
   
    foreach ($attributes as $attribute){
        if($attribute->getAttributecode() != "sku" && $attribute->getAttributecode() != "name") {
            $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute->getAttributecode(), 'is_searchable', false);
        }
    }
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
