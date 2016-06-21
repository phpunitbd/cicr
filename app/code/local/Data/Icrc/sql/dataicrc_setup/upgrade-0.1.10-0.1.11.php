<?php

$installer = $this;
$installer->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/language_available_message', 'Pour obtenir ces langues, non disponibles sur notre E-boutique CICR, veuillez contacter nos délégations.', 'stores', $fr);

$installer->endSetup();

