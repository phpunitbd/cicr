<?php

$this->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$helper->createCmsPage('Terms & conditions', array($en, $int), 'terms-and-conditions');
$helper->createCmsPage('Conditions générales de vente', array($fr), 'terms-and-conditions');

$this->endSetup();

