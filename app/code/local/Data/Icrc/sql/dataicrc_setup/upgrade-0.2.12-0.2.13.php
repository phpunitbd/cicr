<?php

$this->startSetup();

$helper = Mage::helper('data_icrc/update');
list($en, $fr, $int) = $helper->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/contact_us_image', 'images/Bouton-contact-EN.png', 'default', 0);
$conf->saveConfig('icrc/web/contact_us_image', 'images/Bouton-contact-FR.png', 'stores', $fr);

$this->endSetup();

