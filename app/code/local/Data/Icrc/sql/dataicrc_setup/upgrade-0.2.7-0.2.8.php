<?php

$this->startSetup();

$status = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'status');
$status->setIsGlobal(0)->save();

$this->endSetup();

