<?php

$this->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$collection = Mage::getModel('cms/block')->getCollection()
              ->addFieldToFilter('is_active', 1);

foreach ($collection as $block) {
  if ($block->getIdentifier() == 'index_potm') {
    $cnt = preg_replace('/Product of the Month/', 'Our Selection', $block->getContent());
    $cnt = preg_replace('/Produit du mois/', 'Notre sélection', $cnt);
    Mage::getModel('cms/block')->load($block->getId())->setContent($cnt)->save();
  }
}

$this->endSetup();

