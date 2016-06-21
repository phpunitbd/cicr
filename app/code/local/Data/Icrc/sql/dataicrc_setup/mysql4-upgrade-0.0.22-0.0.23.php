<?php

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

$websites = Mage::getModel('core/website')->getCollection();
foreach($websites as $w) {
  if ($w->getCode() == 'internal') {
    $website = $w;
    break;
  }
}

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('carriers/icrc_internal/active', '1', 'websites', $website->getId());
$conf->saveConfig('carriers/icrc/active', '0', 'websites', $website->getId());
$conf->saveConfig('customer/address/prefix_show', null, 'websites', $website->getId());
//$conf->saveConfig('payment/icrc/order_status', 'processing', 'websites', $website->getId());

$conf->saveConfig('carriers/flatrate/active', '0', 'default', 0);
$conf->saveConfig('carriers/tablerate/active', '0', 'default', 0);
$conf->saveConfig('carriers/freeshipping/active', '0', 'default', 0);
$conf->saveConfig('carriers/ups/active', '0', 'default', 0);
$conf->saveConfig('carriers/usps/active', '0', 'default', 0);
$conf->saveConfig('carriers/fedex/active', '0', 'default', 0);
$conf->saveConfig('carriers/dhl/active', '0', 'default', 0);
$conf->saveConfig('carriers/dhlint/active', '0', 'default', 0);

$setup->endSetup();

