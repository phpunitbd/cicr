<?php

$this->startSetup();

function createBlock_45($title, $store, $id, $cnt = null)
{
  try {
    $staticBlock = array(
                'title' => $title,
                'identifier' => $id,
                'content' => $cnt ? $cnt : 'Sample data for block '.$title,
                'is_active' => 1,
                'stores' => array($store)
                );
 
    Mage::getModel('cms/block')->setData($staticBlock)->save();
  }
  catch (Mage_Core_Exception $e) {
    // ignore
  }
}

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$fr_txt = '<h1>Produit du mois</h1>
{{block type="core/template" template="catalog/product/home.phtml" product="test"}}';
$en_txt = '<h1>Product of the Month</h1>
{{block type="core/template" template="catalog/product/home.phtml" product="test"}}';

createBlock_45("Product of the Month for EN", $en, "index_potm", $en_txt);
createBlock_45("Product of the Month for Internal", $int, "index_potm", $en_txt);
createBlock_45("Product of the Month for FR", $fr, "index_potm", $fr_txt);

$this->endSetup();

