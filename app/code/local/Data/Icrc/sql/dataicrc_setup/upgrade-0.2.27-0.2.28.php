<?php
try {
    $installer = new Mage_Core_Model_Resource_Setup('core_setup');
    $installer->run("
        UPDATE `catalog_category_entity_varchar`
        SET value = 'e-Books'
        WHERE attribute_id = (SELECT attribute_id FROM `eav_attribute` WHERE `entity_type_id` = 3 AND `attribute_code` = 'name')
        AND value = 'ebooks'
    ");
    
} catch (Exception $e) {
    // Silence is golden
    Mage::log($e, null, 'local.log', true);
}