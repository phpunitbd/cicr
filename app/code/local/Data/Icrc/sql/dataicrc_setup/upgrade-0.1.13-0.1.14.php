<?php

$this->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('contacts/email/recipient_email', 'shop@icrc.org', 'default', 0);
$conf->saveConfig('customer/create_account/email_domain', 'icrc.org', 'default', 0);

$this->endSetup();

