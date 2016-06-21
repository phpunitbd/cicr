<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);

$attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'film_system');
$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
$attribute->setStoreLabels(array(0 => 'System', $fr => 'Système'));
$attribute->save();

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();

