<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
list($public, $internal) = Mage::helper('data_icrc/update')->getWebsiteIds();
Mage::register('isSecureArea', true);

// Get currency rates as it's needed on install
Data_Icrc_Helper_Debug::msg("getConfigAllowCurrencies");
Data_Icrc_Helper_Debug::dump(Mage::getModel('directory/currency')->getConfigAllowCurrencies());
Data_Icrc_Helper_Debug::msg("getConfigBaseCurrencies");
Data_Icrc_Helper_Debug::dump(Mage::getModel('directory/currency')->getConfigBaseCurrencies());
Mage::helper('data_icrc/update')->loadCurrencies();

// Actually, we create the donations in 0.0.54, so don't create it now, it's better

Mage::unregister('isSecureArea');

$this->endSetup();


