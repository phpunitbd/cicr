<?php

try {
    /* @var $installer Mage_Core_Model_Resource_Setup */
    $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
    $installer->startSetup();

    // Create new order status
    $data = array(
        'status' => 'to_be_validated_by_manager',
        'label' => 'To be validated by your manager'
    );
    $status = Mage::getModel('sales/order_status');
    $status->setData($data)->save();
    
    $data = array(2 => 'En attente validation manager');
    $status->setStoreLabels($data)->save();

    // Assign to some order state
    $status->assignState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);

    $installer->endSetup();
    
} catch (Exception $e) {
    // Silence is golden
    Mage::logException($e);
}
