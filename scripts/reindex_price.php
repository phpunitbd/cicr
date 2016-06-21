<?php

include_once('app/Mage.php');
Mage::app();
echo "starting at ".date('c')."\n";
$indexer = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
$indexer->reindexAll();
echo "done at ".date('c')."\n";


