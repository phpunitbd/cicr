<?php

require_once 'abstract.php';

class Mage_Shell_Related extends Mage_Shell_Abstract
{
  const DEBUG = false;

  public function run() {
    Mage::getSingleton('data_icrc/cron')->related(self::DEBUG);
  }
}

$sh = new Mage_Shell_Related();
$sh->run();

