<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$website = Mage::getModel('core/website')->load(1);
$storeGroup = $website->getDefaultGroup();

$store = Mage::getModel('core/store')->load('fr', 'code');
if (!$store->getId()) {
  $store->setCode('fr')
        ->setWebsiteId($storeGroup->getWebsiteId())
        ->setGroupId($storeGroup->getId())
        ->setName('FR')
        ->setIsActive(1)
        ->save();
} else {
  $store->setName('FR')
        ->setIsActive(1)
        ->save();
}

$store = Mage::getModel('core/store')->load('default', 'code');
if ($store->getId()) {
  $store->setName('EN')
        ->setIsActive(1)
        ->save();
}

$setup->endSetup();

