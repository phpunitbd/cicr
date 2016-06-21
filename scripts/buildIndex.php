<?php

include_once('app/Mage.php');
Mage::app();

$indexer = Mage::getResourceModel('catalog/product_flat_indexer');
$indexer->rebuild();
echo "done\n";