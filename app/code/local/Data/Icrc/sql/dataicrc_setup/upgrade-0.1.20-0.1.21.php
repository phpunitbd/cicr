<?php

$this->startSetup();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'streaming_link');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setIsGlobal(0)->save();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'download_link');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setIsGlobal(0)->save();

$this->endSetup();
