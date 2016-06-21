<?php

$this->startSetup();

$conf = new Mage_Core_Model_Config();
// Set 'Persist Shopping Cart' to No to not merge previously saved shopping cart with current one (but load it if there is no current one)
$conf->saveConfig('persistent/options/shopping_cart', '0', 'default', 0);

$this->endSetup();

