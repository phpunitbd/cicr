<?php

$installer = $this;
$installer->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('customer/captcha/enable', '1', 'default', 0);
$conf->saveConfig('customer/captcha/forms', 'contact,newsletter', 'default', 0);

$installer->endSetup();

