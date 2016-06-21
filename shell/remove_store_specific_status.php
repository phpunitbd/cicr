<?php

require_once 'abstract.php';

class Mage_Shell_Related extends Mage_Shell_Abstract
{
  public function run() {
    $resource = Mage::getSingleton('core/resource');
    $writeConnection = $resource->getConnection('core_write');
    $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'status');
    $statusId = $attribute->getAttributeId();
    $statusTable = $attribute->getBackendTable();
    $statusType = $attribute->getEntityTypeId();
    $sql = "delete from ${statusTable} where entity_type_id = ${statusType} and attribute_id = ${statusId} and store_id != 0";
    $writeConnection->query($sql);
    $indexer = Mage::getSingleton('index/indexer');
    $process = $indexer->getProcessByCode('catalog_product_flat');
    if ($process) $this->reindex($process);
    else foreach ($indexer->getProcessesCollection() as $process) $this->reindex($process);
  }

  protected function reindex($process) {
    try {
      $process->reindexEverything();
      echo $process->getIndexer()->getName() . " index was rebuilt successfully\n";
    } catch (Mage_Core_Exception $e) {
      echo $e->getMessage() . "\n";
    } catch (Exception $e) {
      echo $process->getIndexer()->getName() . " index process unknown error:\n";
      echo $e . "\n";
    }
  }
}

$sh = new Mage_Shell_Related();
$sh->run();

