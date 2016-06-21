<?php

include_once 'app/Mage.php';
Mage::app();

$i = 0;
$ps = Mage::getModel('catalog/product')->getCollection();
foreach ($ps as $pp) {
  echo (++$i % 10) ? '.' : '|';
  if (($i % 100) == 0) echo "\n";
  $p = Mage::getModel('catalog/product')->load($pp->getId());
  if ($p->getTypeId() == 'configurable') {
    if ($p->getStockItem()->getManageStock())
      $p->getStockItem()->setManageStock(0)->setUseConfigManageStock(0)->save();
    continue;
  }
  $stockData = $p->getStockData();
  if (!array_key_exists('qty', $stockData) || $stockData['qty'] < 10000 ||
      !array_key_exists('is_in_stock', $stockData) || !$stockData['is_in_stock']) {
    $stockData['qty'] += 10000;
    $stockData['is_in_stock'] = 1;
    $p->setStockData($stockData)->save();
  }
}

