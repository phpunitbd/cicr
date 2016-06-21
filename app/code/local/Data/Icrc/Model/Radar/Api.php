<?php

class Data_Icrc_Model_Radar_Api extends Mage_Catalog_Model_Api_Resource
{
  public function load($acronym, $delegation, $costcenter, $objective) {
    $resource = Mage::getSingleton('core/resource');
    $write = $resource->getConnection('core_write');
    $write->beginTransaction();
    try {
      $tableName = $resource->getTableName('data_icrc/radar_acronym');
      $write->query("DELETE FROM {$tableName}");
      if (is_object($acronym))
        $acronym = array($acronym);
      foreach ($acronym as $a) {
        Data_Icrc_Helper_Debug::dump($a);
        $_acronym = Mage::getModel('data_icrc/radar_acronym');
        $_acronym->setCode($a->Code)->setName($a->Name)->setFrenchName($a->FrenchName)->save();
      }
      $tableName = $resource->getTableName('data_icrc/radar_delegation');
      $write->query("DELETE FROM {$tableName}");
      if (is_object($delegation))
        $delegation = array($delegation);
      foreach ($delegation as $d) {
        Data_Icrc_Helper_Debug::dump($d);
        $_delegation = Mage::getModel('data_icrc/radar_delegation');
        $_delegation->setMainSiteName($d->MainSiteName)->setName($d->Name)->save();
      }
      $tableName = $resource->getTableName('data_icrc/radar_costcenter');
      $write->query("DELETE FROM {$tableName}");
      if (is_object($costcenter))
        $costcenter = array($costcenter);
      foreach ($costcenter as $c) {
        Data_Icrc_Helper_Debug::dump($c);
        $_costcenter = Mage::getModel('data_icrc/radar_costcenter');
        $_costcenter->setCode($c->Code)->setName($c->Name)->save();
      }
      $tableName = $resource->getTableName('data_icrc/radar_objective');
      $write->query("DELETE FROM {$tableName}");
      if (is_object($objective))
        $objective = array($objective);
      foreach ($objective as $o) {
        Data_Icrc_Helper_Debug::dump($o);
        $_objective = Mage::getModel('data_icrc/radar_objective');
        $_objective->setGOCode($o->GOCode)->setName($o->Name)->save();
      }
      $write->commit();
    } catch (Exception $ex) {
      $write->rollback();
      throw $ex;
    }
    return 'ok';
  }
}

