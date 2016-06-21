<?php

/**
* Sets prefix :
* - required for default website
* - not visible on internal website
*/
$this->startSetup();

list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId = $setup->getEntityTypeId('customer');
$entityTypeIdAddr = $setup->getEntityTypeId('customer_address');
$prefixId = Mage::getModel('customer/attribute')->loadByCode($entityTypeId, 'prefix')->getId();
$prefixIdAddr = Mage::getModel('customer/attribute')->loadByCode($entityTypeId, 'prefix')->getId();

$prefix = Mage::getModel('customer/attribute')->setWebsite($internal)->load($prefixId)->setWebsite($internal);
$prefix->setIsVisible(false)->setIsRequired(false)->save();

$prefix = Mage::getModel('customer/attribute')->load($prefixId);
$prefix->setIsVisible(true)->setIsRequired(true)->save();

$prefixAddr = Mage::getModel('customer/attribute')->setWebsite($internal)->load($prefixIdAddr)->setWebsite($internal);
$prefixAddr->setIsVisible(false)->setIsRequired(false)->save();

$prefixAddr = Mage::getModel('customer/attribute')->load($prefixIdAddr);
$prefixAddr->setIsVisible(true)->setIsRequired(true)->save();

$this->endSetup();

