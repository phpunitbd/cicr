<?php

$this->startSetup();

Mage::register('isSecureArea', true);

$allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow', 0));

foreach (array('SS', 'XZ') as $more)
  if (!in_array($more, $allowCountries))
    $allowCountries[] = $more;

sort($allowCountries);

$conf = new Mage_Core_Model_Config();
$conf->saveConfig('general/country/allow', implode(',', $allowCountries), 'default', 0);

// Do not forget to unset ...
Mage::unregister('isSecureArea');

$this->endSetup();


