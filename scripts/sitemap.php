<?php

require_once('app/Mage.php');
Mage::app();

$c = Mage::getModel('sitemap/sitemap')->getCollection();
foreach ($c as $s) {

  var_dump($s);

}

