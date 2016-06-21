<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();

$categories = Mage::getModel('catalog/category')->getCollection();
$categories->addAttributeToFilter('name', 'Donations');
foreach ($categories as $cat) {
  $donation_cat = $cat->getId();
}

$tmp = Mage::getModel('eav/entity_attribute_set')->load('donation', 'attribute_set_name');
$donation_set = $tmp->getId();

// Must be in secure area to add/delete products
$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
$baseCurrency = Mage::getModel('directory/currency')->load($baseCurrencyCode);
Mage::register('isSecureArea', true);

Mage::helper('data_icrc/update')->loadCurrencies();
$helper = Mage::helper('directory');

$old = Mage::getModel('catalog/category')->load($donation_cat)->getProductCollection();
$deleted = array();
foreach ($old as $p) {
  $deleted[] = $p->getId();
  $p->delete();
}
$id_deleted = implode(',', $deleted);
foreach (array($en, $fr, $int) as $store) {
  $sql = "delete from catalog_product_flat_${store} where entity_id in (${id_deleted})";
}

$currency = array('CHF', 'EUR', 'USD');
$value = array(10, 20, 30, 40);
foreach ($currency as $c) {
  $rate = (float)$baseCurrency->getRate($c);
  if ($rate == 0) $rate = 1;
  foreach ($value as $v) {
    $desc = "Donation: $v $c";
    $sku = Data_Icrc_Block_Checkout_Donation::PREFIX . $c . '-' . $v;

    $sProduct = Mage::getModel('catalog/product');
    $sProduct 
      ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
      ->setWebsiteIds(array($public))
      ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
      ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
      ->setTaxClassId(0)
      ->setAttributeSetId($donation_set)
      ->setCategoryIds(array($donation_cat))
      ->setSku($sku)
      ->setName($desc) 
      ->setShortDescription($desc) 
      ->setDescription($desc) 
      ->setWeight(0)
      ->setProductionCost($v);
    if ($c == $baseCurrencyCode)
      $sProduct->setPrice($v);
    else
      $sProduct->setPrice($v / $rate);
    $sProduct->setStockData(array(
        'is_in_stock' => 1,
        'use_config_max_sale_qty' => 1,
        'use_config_max_sale_qty' => 1,
        'use_config_manage_stock' => 0,
        'manage_stock' => 0
    ));
    $sProduct->save();
  }
}

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();


