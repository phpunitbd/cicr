<?php

/**
* Sets prefix :
* - required for default website
* - not visible on internal website
*/
$this->startSetup();

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'contentdate');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setUsedInProductListing(1)->save();

$this->endSetup();

