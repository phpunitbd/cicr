<?php

require_once('app/Mage.php');
Mage::app();

$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

$attrs = Mage::getResourceModel('eav/attribute')->getCollection();
foreach ($attrs as $a) {
  var_dump($a->getData());
}

$configurables = Mage::getModel('catalog/product')->getCollection()->
                  addAttributeToSelect('sku')->addFieldToFilter('type_id', 'configurable');
foreach ($configurables as $c) {
  #echo $c->getId() . "\n";
  $simples = Mage::getModel('catalog/product')->getCollection()->
    addAttributeToSelect('lang')->addAttributeToSelect('system')->
    addFieldToFilter('sku', array('like' => $c->getSku() . '/%'));
  $data = array();
  $ids = array($c->getId());
  foreach ($simples as $s) {
    $ids[] = $s->getId();
    #echo " - " . $s->getId() . "\n";
    if ($s->getLang()) {
      $attrid = 'LANGID';
      $value = $s->getLang();
    } else {
      $attrid = 'SYSTEMID';
      $value = $s->getSystem();
    }
    $link = array('attribute_id' => $attrid, 'value_index' => $value);
    $data[$s->getId()] = array('0' => $link);
  }
  var_dump($data);break;
  $writeConnection->query("delete from catalog_product_super_attribute where product_id in (" . implode(',', $ids) . ")");
  $writeConnection->query("delete from catalog_product_super_link where parent_id in (" . implode(',', $ids) . ")");
  $product = Mage::getModel('catalog/product')->load($c->getId());
  #var_dump($product->getConfigurableProductsData());
  $product->setConfigurableProductsData($data)->save();
  break;
}

