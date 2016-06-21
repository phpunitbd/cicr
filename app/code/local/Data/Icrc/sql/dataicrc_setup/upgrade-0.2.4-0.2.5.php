<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/contact_message', "Bonjour cher client. Bienvenue sur le ICRC shop.
Veuillez remplir le formulaire ci-dessous, nous vous répondrons dans les plus brefs délais.", 'stores', $fr);

$this->endSetup();

