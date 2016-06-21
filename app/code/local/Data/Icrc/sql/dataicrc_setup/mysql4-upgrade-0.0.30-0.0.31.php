<?php

$installer = $this;
$installer->startSetup();

$categories = Mage::getModel('catalog/category')->getCollection();
$categories->addAttributeToFilter('name', 'Donations');
foreach ($categories as $cat) {
  $cat->setIncludeInMenu(0)->save();
}

$installer->endSetup();

