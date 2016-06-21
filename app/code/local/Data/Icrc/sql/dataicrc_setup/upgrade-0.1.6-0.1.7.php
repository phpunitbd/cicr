<?php

$installer = $this;
$installer->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/language_available_message', 'Pour obtenir ces langues (non disponibles sur le site), veuillez nous contacter', 'stores', $fr);
$conf->saveConfig('design/email/logo', 'default/Logo-ICRC.png', 'default', 0);
$conf->saveConfig('design/email/logo', 'default/Logo-CICR.png', 'stores', $fr);

$installer->endSetup();

