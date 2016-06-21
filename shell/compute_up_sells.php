<?php

require_once 'abstract.php';

class Mage_Shell_UpSell extends Mage_Shell_Abstract
{
  const DEBUG = false;

  public function run() {
    try {
      $cron = Mage::getSingleton('data_icrc/cron');
      $cron->upsell(self::DEBUG);
    } catch (Exception $e) {
      echo $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
      die($e->getMessage());
    }
  }
}

$shell = new Mage_Shell_UpSell();
$shell->run();

