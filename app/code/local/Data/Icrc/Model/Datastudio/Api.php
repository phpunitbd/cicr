<?php

class Data_Icrc_Model_Datastudio_Api extends Mage_Catalog_Model_Api_Resource
{
  public function log($sessionNumber, $status, $message) {
    $_event = Mage::getModel('data_icrc/datastudio_log');
    $_event->setSessionId($sessionNumber)
           ->setStatus($status)
           ->setMessage($message)
           ->save();
    return 'ok';
  }
}

