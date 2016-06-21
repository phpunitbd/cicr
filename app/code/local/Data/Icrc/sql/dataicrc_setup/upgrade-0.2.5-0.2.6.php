<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$conf = new Mage_Core_Model_Config();
$conf->saveConfig('icrc/web/contact_message', "Les expositions ne peuvent pas être commandées directement.
Veuillez remplir le formulaire suivant pour en commander une ; nous vous répondrons dans les meilleurs délais.", 'stores', $fr);

$this->endSetup();

