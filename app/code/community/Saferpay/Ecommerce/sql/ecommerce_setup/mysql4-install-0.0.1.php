<?php

	$magentoVersion = Mage::getVersionInfo();
	if($magentoVersion['major'] < 2 && $magentoVersion['minor'] < 5){

		// Installer is not needed for versions older then 1.5.0.0

	}else{

		$installer = $this;

		$statusTable        = $installer->getTable('sales/order_status');
		$statusStateTable   = $installer->getTable('sales/order_status_state');
		$statusLabelTable   = $installer->getTable('sales/order_status_label');

		$data = array(
			array('status' => 'authorized', 'label' => 'Authorized Payment')
		);
		$installer->getConnection()->insertArray($statusTable, array('status', 'label'), $data);

		$data = array(
			array('status' => 'authorized', 'state' => 'authorized', 'is_default' => 1)
		);
		$installer->getConnection()->insertArray($statusStateTable, array('status', 'state', 'is_default'), $data);
	}

?>