<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$website = Mage::getModel('core/website');
$website->setCode('internal')
        ->setName('internal')
        ->save();

$storeGroup = Mage::getModel('core/store_group');
    $storeGroup->setWebsiteId($website->getId())
        ->setName('internal')
        ->setRootCategoryId(2)
        ->save();

$store = Mage::getModel('core/store');
    $store->setCode('internal')
        ->setWebsiteId($storeGroup->getWebsiteId())
        ->setGroupId($storeGroup->getId())
        ->setName('internal')
        ->setIsActive(1)
        ->save();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('catalog/price/scope', '1', 'default', 0);
$conf->saveConfig('currency/options/base', 'CHF', 'websites', $website->getId());
$conf->saveConfig('payment/saferpay_creditcard/active', '0', 'websites', $website->getId());
$conf->saveConfig('payment/icrc/active', '1', 'websites', $website->getId());
//$conf->saveConfig('payment/icrc/order_status', 'processing', 'websites', $website->getId());

$setup->endSetup();

