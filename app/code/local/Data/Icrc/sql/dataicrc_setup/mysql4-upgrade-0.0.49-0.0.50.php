<?php

$this->startSetup();

$data_cms = array('exchange', 'legal-notices', 'newsletter', 
                  'payment-policy-security', 'pricing-policy', 
                  'shipping-policy', 'privacy-policy');

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$cms = Mage::getModel('cms/page')->getCollection()->addStoreFilter($int);
foreach ($cms as $p) {
  if (!in_array($p->getIdentifier(), $data_cms))
    continue;
  $stores = $p->getResource()->lookupStoreIds($p->getId());
  if (count($stores) == 1 && $stores[0] == $int)
    $p->delete();
}

$cms = Mage::getModel('cms/page')->getCollection()->addStoreFilter($en);
foreach ($cms as $p) {
  if (!in_array($p->getIdentifier(), $data_cms))
    continue;
  $stores = $p->getResource()->lookupStoreIds($p->getId());
  if (count($stores) == 1 && $stores[0] == $en) {
    $tmp = Mage::getModel('cms/page')->load($p->getId());
    if ($p->getRootTemplate() != 'one_column')
      $tmp->setRootTemplate('one_column');
    $tmp->setStores(array($en, $int))->save();
  }
}

$cms = Mage::getModel('cms/page')->getCollection();
foreach ($cms as $p) {
  if ($p->getRootTemplate() != 'one_column') {
    $tmp = Mage::getModel('cms/page')->load($p->getId());
    $tmp->setRootTemplate('one_column')->save();
  }
}

$this->endSetup();

