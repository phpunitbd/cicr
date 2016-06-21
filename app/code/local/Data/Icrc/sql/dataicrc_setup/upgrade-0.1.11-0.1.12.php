<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$cms = Mage::getModel('cms/page')->getCollection()->addStoreFilter($fr);
foreach ($cms as $p) {
  if ($p->getIdentifier() != 'shipping-policy')
    continue;
  $tmp = Mage::getModel('cms/page')->load($p->getId());
  $tmp->setTitle('Tarifs & Délais de livraison')->setStores($fr)->save();
  break;
}


$this->endSetup();

