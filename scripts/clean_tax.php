<?php

require_once('app/Mage.php');
Mage::app();

$tax_classes = Mage::getModel('tax/class')->getCollection();
foreach ($tax_classes as $tax_class) {
  if ($tax_class->getClassName() == 'Reduced Taxable Goods' && $tax_class->getClassType() == 'PRODUCT') {
    $tax_class->delete();
  }
}

foreach (Mage::getModel('tax/calculation')->getCollection() as $c) {
  $c->delete();
}

foreach (Mage::getModel('tax/calculation_rule')->getCollection() as $u) {
  $u->delete();
}

foreach (Mage::getModel('tax/calculation_rate')->getCollection() as $a) {
  $a->delete();
}
