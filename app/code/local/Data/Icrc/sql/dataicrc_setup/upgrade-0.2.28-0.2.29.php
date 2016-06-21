<?php
try {
    $installer = new Mage_Core_Model_Resource_Setup('core_setup');
    $installer->setConfigData('tax/sales_display/full_summary', 1);
    $installer->setConfigData('sales/identity/address', 'International Committee of the Red Cross
19 Avenue de la paix CH 1202 Geneva
Swizerland
V.A.T. number : CH-105.924.024');
    $installer->setConfigData('sales/identity/address', 'International Committee of the Red Cross
19 Avenue de la paix CH 1202 Geneva
Swizerland
N° TVA :  CH-105.924.024', 'stores', 2);
    
} catch (Exception $e) {
    // Silence is golden
    Mage::log($e, null, 'local.log', true);
}