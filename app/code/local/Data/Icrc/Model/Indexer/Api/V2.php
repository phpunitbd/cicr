<?php

class Data_Icrc_Model_Indexer_Api_V2 extends Mage_Catalog_Model_Api_Resource
{
  public function run($indexerId) {
    if (empty($indexerId)) {
      Mage::getSingleton('index/indexer')->getProcessesCollection()->walk('reindexAll');
      return 'all';
    }
    $res = array();
    foreach (explode(',', $indexerId) as $id) {
      if (is_numeric($id)) {
        $indexer = Mage::getSingleton('index/indexer')->getProcessById($id);
        if (!$indexer || !$indexer->getId())
          continue;
        $res[] = (int)$id;
        $indexer->reindexAll();
      }
      else {
        $indexer = Mage::getSingleton('index/indexer')->getProcessByCode($id);
        if (!$indexer || !$indexer->getId())
          continue;
        $res[] = $indexer->getId();
        $indexer->reindexAll();
      }
    }
    if (count($res)) return join(',', $res);
    return 'false';
  }
}

