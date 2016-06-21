<?php

$this->startSetup();

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('payment/free/payment_action', 'authorize_capture', 'default', 0);

$this->endSetup();

