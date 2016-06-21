<?php

$installer = $this;
$installer->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('currency/import/error_email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_general/email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_general/name', 'ICRC Shop', 'default', 0);
$conf->saveConfig('trans_email/ident_sales/email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_sales/name', 'ICRC Shop Sales', 'default', 0);
$conf->saveConfig('trans_email/ident_support/email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_support/name', 'ICRC Shop Support', 'default', 0);
$conf->saveConfig('trans_email/ident_custom1/email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_custom1/name', 'ICRC Shop', 'default', 0);
$conf->saveConfig('trans_email/ident_custom2/email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('trans_email/ident_custom2/name', 'ICRC Shop', 'default', 0);

$installer->endSetup();

