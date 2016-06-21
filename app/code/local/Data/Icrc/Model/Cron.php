<?php

class Data_Icrc_Model_Cron extends Mage_Core_Model_Abstract
{
  public function __construct()
  {
    parent::__construct();
    $this->prep_raise_memory_limit(1024);
  }

  public function related($debug = false) {
    Mage::getSingleton('core/session')->setUpdatedAt(0);
    $bb = microtime(true);
    if ($debug) echo '+';
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $writeConnection = $resource->getConnection('core_write');
    $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'theme');
    $pTable = $resource->getTableName('catalog/product');
    $pTableV = $pTable . '_' . $attribute->getBackendType();
    $oiTable = $resource->getTableName('sales/order_item');
    $attId = $attribute->getAttributeId();
    if ($debug) echo '+';

    // First split themes in a temp table
    $query_cursor = "SELECT entity_id, value theme FROM $pTableV WHERE attribute_id = $attId AND value IS NOT NULL";
    $writeConnection->query("DROP TABLE IF EXISTS tmp_product_theme");
    $writeConnection->query("CREATE TABLE tmp_product_theme ( id INT, theme INT ) Engine=memory");
    $writeConnection->query("DROP PROCEDURE IF EXISTS split_theme_in_tmp");
    $writeConnection->query("CREATE PROCEDURE split_theme_in_tmp ()
BEGIN
  DECLARE remainder TEXT;
  DECLARE currentSelection TEXT;
  DECLARE tempT TEXT;
  DECLARE delim VARCHAR(2) DEFAULT ',';
  DECLARE iDone INTEGER (11) UNSIGNED DEFAULT 0;
  DECLARE product_id INTEGER (10) UNSIGNED;
  DECLARE productIterator CURSOR FOR ${query_cursor};
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET iDone = 1;
  OPEN productIterator;
    products: LOOP
      FETCH productIterator INTO product_id, remainder;
      IF 1 = iDone THEN
        LEAVE products;
      END IF;
      WHILE CHAR_LENGTH(remainder) > 0 DO
        SELECT SUBSTRING_INDEX(remainder, delim, 1) INTO currentSelection;
        SELECT SUBSTRING(remainder FROM char_length(currentSelection) + 1 + length(delim)) INTO tempT;
        INSERT INTO tmp_product_theme(id, theme) VALUES (product_id, currentSelection);
        SET remainder = tempT;
      END WHILE;
    END LOOP products;
  CLOSE productIterator;
END");
    if ($debug) echo '+';
    $writeConnection->query("CALL split_theme_in_tmp");
    if ($debug) echo '+';

    // Then links products with common themes, joining with sales to order them
    $sql = "SELECT DISTINCT p.entity_id, p2.entity_id product_id, SUM(oi.qty_ordered) s
      FROM $pTable p, tmp_product_theme t, tmp_product_theme t2, $pTable p2
        LEFT JOIN $oiTable oi ON oi.product_id = p2.entity_id
      WHERE 
        t.theme = t2.theme
        AND p.entity_id = t.id
        AND p2.entity_id = t2.id
        AND p.entity_id != p2.entity_id
        AND p.type_id = 'configurable'
        AND p2.type_id = 'configurable'
        AND p2.sku NOT LIKE '%-ebook'
      GROUP BY p.entity_id, p2.entity_id
      ORDER BY p.entity_id, s DESC, p2.entity_id";
    $rs = $readConnection->query($sql);
    if ($debug) echo '+';
    $mm = microtime(true);

    // And put the 10 firsts in relations
    $last = -1;
    $count = 0;
    $ids = array();
    foreach ($rs as $r) {
      if ($r['entity_id'] != $last) {
        $this->_doLinkRelated($last, $ids, $debug);
        $last = $r['entity_id'];
        $count = 0;
        $ids = array();
      }
      if ($count++ >= 10)
        continue;
      $ids[$r['product_id']] = array('position' => count($ids));
    }
    $this->_doLinkRelated($last, $ids, $debug);
    $ee = microtime(true);
    if ($debug) {
      $t = $ee - $bb;
      $i = $ee - $mm;
      echo " - total time: ${t}s, insert time: ${i}s\n";
    }
    Mage::getSingleton('core/session')->unsUpdatedAt();
  }

  public function upsell($debug = false) {
    Mage::getSingleton('core/session')->setUpdatedAt(0);
    $bb = microtime(true);
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');

    $pTable = $resource->getTableName('catalog/product');
    $oiTable = $resource->getTableName('sales/order_item');

    $query = "select p2.entity_id, oi2.product_id, sum(oi2.qty_ordered) s
    from $oiTable oi2, $pTable p2
    where oi2.product_type = 'configurable' and p2.entity_id != oi2.product_id and
    exists (select 1 
            from $oiTable oi 
            where oi.product_type = 'configurable' and 
                  oi.product_id = p2.entity_id and oi.order_id = oi2.order_id ) and
    exists (select 1 from $pTable p3 where p3.entity_id = oi2.product_id) -- check if upsell poduct already exists
    group by p2.entity_id, oi2.product_id order by p2.entity_id, s desc";

    $rs = $readConnection->query($query);
    $mm = microtime(true);

    $last = -1;
    $count = 0;
    $ids = array();
    foreach ($rs as $r) {
      if ($r['entity_id'] != $last) {
        $this->_doLinkUpsell($last, $ids, $debug);
        $last = $r['entity_id'];
        $count = 0;
        $ids = array();
      }
      if ($count++ >= 10)
        continue;
      $ids[$r['product_id']] = array('position' => count($ids));
    }
    $this->_doLinkUpsell($last, $ids, $debug);
    $ee = microtime(true);
    if ($debug) {
      $t = $ee - $bb;
      $i = $ee - $mm;
      echo " - total time: ${t}s, insert time: ${i}s\n";
    }
    Mage::getSingleton('core/session')->unsUpdatedAt();
  }

  private function _doLinkRelated($id, $rels, $debug = false) {
    if (count($rels) && $id > 0) {
      // Do insert
      $_product = Mage::getModel('catalog/product')->load($id);
      if(!$_product->getManualConfiguration()) {
        $_product->setRelatedLinkData($rels)->save();
      }
      if ($debug) echo '.';
    }
  }

  private function _doLinkUpsell($id, $rels, $debug = false) {
    if (count($rels) && $id > 0) {
      // Do insert
      $_product = Mage::getModel('catalog/product')->load($id);
      if(!$_product->getManualConfiguration()) {
        $_product->setUpSellLinkData($rels)->save();
      }
      if ($debug) echo '.';
    }
  }

  protected function prep_raise_memory_limit_try($overload, $test, $min) {
    $res = ini_set('memory_limit', round($test) . 'M');
    if ($res === false) {
      $diff = $test - $min;
      if ($diff < 3) return;
      $this->prep_raise_memory_limit_try($test, $min + ($diff / 2), $min);
    }
    else {
      $diff = $overload -	$test;
      if ($diff < 3) return;
      $this->prep_raise_memory_limit_try($overload, $test + ($diff / 2), $test);
    }
  }

  protected function prep_raise_memory_limit($request) {
    $res = ini_set('memory_limit', round($request) . 'M');
    if ($res === false) {
      $this->prep_raise_memory_limit_try($request, $request * 0.6, 128);
      // If this warning shows, consider raising suhosin.memory_limit
      error_log('Warning: cannot set memory_limit, memory stays at ' . ini_get('memory_limit'));
    }
  }
  
  public function datastudioInterfaceWatchdog(Mage_Cron_Model_Schedule $sched) {
    Data_Icrc_Helper_Debug::msg("datastudioInterfaceWatchdog: " . Mage::getStoreConfig('icrc/datastudio/watchdog'));
    if (Mage::getStoreConfig('icrc/datastudio/watchdog') != 1)
      return;
    $collection = Mage::getModel('data_icrc/datastudio_log')->getCollection();
    $collection->getSelect()->where('`date` > DATE_SUB(NOW(), INTERVAL 2 HOUR)');
    $collection->load();
    Data_Icrc_Helper_Debug::msg("getSize: " . $collection->getSize());
    if ($collection->getSize() == 0) {
      $mail = Mage::getModel('core/email');
      $mail->setToName(Mage::getStoreConfig('trans_email/ident_general/name'))
           ->setToEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
           ->setBody('Warning!

There hasn\'t been any Datastudio event for the last 2 hours.

An ICRC administrator should check the reason why in datastudio interface logs.

This is an automatic email, please don\'t reply to it.
')
           ->setSubject('E-Commerce Interface Watchdog Warning')
           ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
           ->setFromName('Datastudio Interface Watchdog')
           ->setType('text');
      try {
        $mail->send();
        Data_Icrc_Helper_Debug::msg('email sent !');
        $mail->setToName('ICRC DBAdmin')->setToEmail('dbadmin@icrc.org')->send();
      }
      catch (Exception $ex) {
        error_log('Cannot send email: ' . $ex->getMessage());
      }
    } else {
      Data_Icrc_Helper_Debug::msg('Watchdog OK ...');
    }
  }

  public function cancelOldExternalPendingPayment(Mage_Cron_Model_Schedule $sched, $before = null) {
    Data_Icrc_Helper_Debug::msg('Cron::cancelOldExternalPendingPayment()');
    if ($before == null)
      $before = date(DATE_ATOM, time() - 86400);
    elseif (is_numeric($before))
      $before = date(DATE_ATOM, time() - (int)$before);

    $orders = Mage::getModel('sales/order')
                    ->getCollection()
                    ->addAttributeToFilter('status', 'pending_payment')
                    ->addAttributeToFilter('updated_at', array('date' => true, 'to' => $before));

    $comment = 'This order was in pending payment status for too long';
    
    $internal = Mage::helper('data_icrc/internal')->getInternalId();

    Data_Icrc_Helper_Debug::msg($orders->count() . ' orders to check ...');
    foreach ($orders as $order) {
      $_order = Mage::getModel('sales/order')->load($order->getId());
      if ($_order->getStoreId() == $internal)
        continue;
      $_quote = Mage::getModel('sales/quote')->load($_order->getQuoteId());
      if ($_quote && $_quote->getId())
        Mage::helper('checkout')->sendPaymentFailedEmail($_quote, $comment);
      $_order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $comment, true)
            ->save();
    }
  }
}

