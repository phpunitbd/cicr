<?php
try {
    $installer = new Mage_Core_Model_Resource_Setup('core_setup');
    $installer->setConfigData('catalog/frontend/default_sort_by', 'contentdate');
} catch (Exception $e) {
    // Silence is golden
    Mage::log($e, null, 'local.log', true);
}