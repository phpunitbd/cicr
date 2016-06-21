<?php

$this->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/cart_image', 'images/icrc-small-cart.png', 'default', 0);
$conf->saveConfig('icrc/web/big_cart_image', 'images/icrc-cart.png', 'default', 0);
$conf->saveConfig('icrc/web/add_to_cart_image', 'images/Bouton-order-EN-2.png', 'default', 0);
$conf->saveConfig('icrc/web/add_to_big_cart_image', 'images/Bouton-order-EN-2.png', 'default', 0);
$conf->saveConfig('icrc/web/cart_image', 'images/cicr-small-cart.png', 'stores', $fr);
$conf->saveConfig('icrc/web/big_cart_image', 'images/cicr-cart.png', 'stores', $fr);
$conf->saveConfig('icrc/web/add_to_cart_image', 'images/Bouton-order-FR-2.png', 'stores', $fr);
$conf->saveConfig('icrc/web/add_to_big_cart_image', 'images/Bouton-order-FR-2.png', 'stores', $fr);

$this->endSetup();

