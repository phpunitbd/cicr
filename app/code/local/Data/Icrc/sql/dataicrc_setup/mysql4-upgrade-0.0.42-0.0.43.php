<?php

$this->startSetup();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setIsFilterable(0)->save();

$this->endSetup();

