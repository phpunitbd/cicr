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

    $attributeCode = 'manual_configuration';
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode, array(
        'label' => 'Manual Configuration',
        'input' => 'boolean',
        'type' => 'int',
        'source' => 'eav/entity_attribute_source_boolean',
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
        'sort_order' => 210,
        'unique' => false,
        'used_for_sort_by' => false,
        'used_in_product_listing' => true,
        'user_defined' => true,
        'visible' => true,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
        'wysiwyg_enabled' => false,
    ));

    foreach ($attributeSetsName as $attributeSetName) {
        $installer->addAttributeToSet(
            Mage_Catalog_Model_Product::ENTITY,     // Entity type
            $attributeSetName,                      // Attribute set name
            'General',                              // Attribute set group name
            $attributeCode,                         // Attribute code to add
            100                                     // Position on the attribute set group
        );
    }
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
