<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
    
    // Ajouter attribut price_all pour tri produit groupé

    $attributeSetsName = array(
        'publications',
        'films',
        'gift',
        'donation',
        'ebook',
        'exhibition',
        'Default'
    );

    $attributeCode = 'price_all';
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode, array(
        'label' => 'Prix total',
        'input' => 'price',
        'type' => 'decimal',
        'backend_model' => 'catalog/product_attribute_backend_price',
        'required' => false,
        'comparable' => false,
        'filterable' => false,
        'filterable_in_search' => false,
        'used_for_promo_rules' => false,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'is_configurable' => false,
        'is_html_allowed_on_front' => true,
        'note' => '',
        'searchable' => false,
        'sort_order' => 100,
        'unique' => false,
        'used_for_sort_by' => true,
        'used_in_product_listing' => true,
        'user_defined' => true,
        'visible' => true,
        'visible_on_front' => true,
        'visible_in_advanced_search' => false,
        'wysiwyg_enabled' => false,
    ));

    foreach ($attributeSetsName as $attributeSetName) {
        $installer->addAttributeToSet(
            Mage_Catalog_Model_Product::ENTITY,     // Entity type
            $attributeSetName,                      // Attribute set name
            'Prices',                               // Attribute set group name
            $attributeCode,                         // Attribute code to add
            10                                      // Position on the attribute set group
        );
    }
    
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'price', 'used_for_sort_by', false);
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
