<?php
//error_log("Antalis.desadv.php starting");

if (file_exists('../app/Mage.php'))
  include_once('../app/Mage.php');
elseif (file_exists('app/Mage.php'))
  include_once('app/Mage.php');
else
  include_once('app/Mage.php');
Mage::app();

//error_log("Antalis.desadv.php: Mage::app() started");

if ($_SERVER['REQUEST_METHOD'] != 'POST' && php_sapi_name() != 'cli') {
	header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed', true, 405);
	error_log('desadv:method '.$_SERVER['REQUEST_METHOD'].' used on interface');
	die("You must POST on this URL\n");
}

global $xml;
$xml = file_get_contents('php://input');
//$xml = file_get_contents('/home/icrc/preprod/magento/interfaces/test.xml');
//$xml = file_get_contents('/tmp/input-xml-213.193.117.196-2013-05-14T14:38:01+02:00.xml');
//$xml = file_get_contents('/tmp/input-xml-test.xml');
//$xml = file_get_contents('/tmp/desadv');
$doc = new DOMDocument;
$ret = $doc->loadXML($xml);

function end_error($msg, $code = null, $status = 'Bad Request') {
  if ($code === null) // id $code === null (not specified), 
    header($_SERVER['SERVER_PROTOCOL'] . ' '.$code.' Bad Request', true, 400);
  elseif ($code !== 0)
    header($_SERVER['SERVER_PROTOCOL'] . ' '.$code.' '.$status, true, $code);
  Data_Icrc_Helper_Debug::msg('end_error('.$msg.', '.$code.' '.$status.')');
  error_log('desadv:end_error('.$msg.', '.$code.' '.$status.')');
  global $xml;
  error_log($xml, 1, 'support-hosting@data.fr');
  die($msg);
}

if (Data_Icrc_Helper_Debug::isDebug()) {
  $i = 0;
  $client = $_SERVER['REMOTE_ADDR'];
  $date = date(DateTime::ATOM);
  $filename = "/tmp/input-xml-${client}-${date}.xml";
  while (file_exists($filename)) {
    $i++;
    $filename = "/tmp/input-xml-${client}-${date}-${i}.xml";
  }
  $doc->save($filename);
}

if ($ret == false) {
  end_error("Cannot parse XML\n");
}
else {
  $xpath = new DOMXpath($doc);
  $elements = $xpath->query("/DELVRY03/IDOC/E1EDL20/E1EDL24");
  $vsart=null; //How we deliver the goods
  $matnr=null; //The ordered material number
  $lfimg=null; //Delivered Quantity
  $bstnr=null; //IKRK Order number
  $posex=null; //IKRK Order Line number
  
  $shippedOrders=array();
  if (!is_null($elements)) {
    foreach ($elements as $element){
      $node = $element->getElementsByTagName('MATNR');
      if (!is_null($node) && !is_null($node->item(0))){
	      $matnr=$node->item(0)->nodeValue;
      }
      $node = $element->getElementsByTagName('VSART');
      if(!is_null($node) && !is_null($node->item(0))){
	      $vsart=$node->item(0)->nodeValue;
      }
      $node = $element->getElementsByTagName('LFIMG');
      if(!is_null($node) && !is_null($node->item(0))){
	      $lfimg=$node->item(0)->nodeValue;
      }
      $node = $element->getElementsByTagName('BSTNR');
      if(!is_null($node) && !is_null($node->item(0))){
	      $bstnr=$node->item(0)->nodeValue;
      }
      $node = $element->getElementsByTagName('POSEX');
      if(!is_null($node) && !is_null($node->item(0))){
	      $posex=$node->item(0)->nodeValue;
      }
      if(!is_null($matnr) && !is_null($lfimg) && !is_null($bstnr)) {
	      $orderId=substr($bstnr,1);
	      if(!array_key_exists($orderId,$shippedOrders)) {
	        $shippedOrders[$orderId]=array();
	      }
	      //$shippedOrders[$orderId]=array_merge($shippedOrders[$orderId],array($matnr => $lfimg));
	      $shippedOrders[$orderId][] = array('matnr' => $matnr, 'lfimg' => $lfimg, 'posex' => $posex);
      }
      Data_Icrc_Helper_Debug::msg("produit trouvé : $matnr quatité : $lfimg");
    }
  } else
    end_error('No elements found in XML', 424, 'No Elements');
  
  Data_Icrc_Helper_Debug::dump($shippedOrders);
  
  foreach($shippedOrders as $shippedOrderId => $shippedOrderItems){
    $isPartial=false;
    $mageOrder = Mage::getModel('sales/order')->loadByIncrementId($shippedOrderId);
    if (!$mageOrder->getId()) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 420 Order not found', true, 420);
      end_error("Order $shippedOrderId not found", 0);
    }
    if (!$mageOrder->canShip()) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 421 Cannot ship', true, 421);
      end_error("Cannot do shipment for order $shippedOrderId", 0);
    }
    $convertOrder = new Mage_Sales_Model_Convert_Order();
    $shipment = $convertOrder->toShipment($mageOrder);
    $mageOrderItems = $mageOrder->getAllItems();
    
    $totalQtyToShip=0;
    foreach ($mageOrderItems as $mageOrderItem) {
      $totalQtyToShip+=$mageOrderItem->getQtyToShip();
    }
    Data_Icrc_Helper_Debug::msg("mageOrder item count =".count($mageOrderItems));
    $totalQty = 0;
    $isShipmentOk=true;
    $posex_handled=array();
    foreach($shippedOrderItems as $_item){
      $shipedOrderItemSku = $_item['matnr'];
      $shippedOrderItemQty = $_item['lfimg'];
      $link = Mage::getModel('data_icrc/sku_mapping')->loadByAntalisSku($shipedOrderItemSku);
      if ($link->getId()) {
	      $newsku = $link->getMagentoSku();
      } else {
	      $newsku = $shipedOrderItemSku; // should fail
      }
      Data_Icrc_Helper_Debug::msg("item: sku($shipedOrderItemSku -> $newsku), qty($shippedOrderItemQty)\n");
      $shippedProductId = Mage::getModel('catalog/product')->getIdBySku($newsku);
      Data_Icrc_Helper_Debug::msg("shippedProductId = $shippedProductId");
      $mageOrderItemFound=null;
      foreach ($mageOrderItems as $mageOrderItem) {
	      if ($mageOrderItem->getProductId()==$shippedProductId) {
	        if (in_array($mageOrderItem->getQuoteItemId(), $posex_handled))
	          continue;
	        $mageOrderItemFound=$mageOrderItem;
	        $posex_handled[]=$mageOrderItem->getQuoteItemId();
	        break;
	      }
      }
      //$mageOrderItem=$mageOrder->getItemById($shippedOrderItemId);
      if(!$mageOrderItemFound){
	      $isShipmentOk=false;
	      $shipmentError = "Cannot find item $shipedOrderItemSku ($newsku)";
	      Data_Icrc_Helper_Debug::msg("shipment ko");
	      break;
      }
      else {
	      $mageOrderShippedItemParentId=$mageOrderItemFound->getParentItemId();
	      Data_Icrc_Helper_Debug::msg("mageOrderShippedItemParentId=$mageOrderShippedItemParentId\n");
	      $mageOrderParentItemFound=null;
	      if(!is_null($mageOrderShippedItemParentId)){
	        $mageOrderParentItemFound=$mageOrder->getItemById($mageOrderShippedItemParentId);
	      }
	
	      if($mageOrderParentItemFound){
	        $mageOrderQty=$mageOrderParentItemFound->getQtyToShip();
	        Data_Icrc_Helper_Debug::msg("mageOrderQtyToShip=$mageOrderQty\n");
	        //Data_Icrc_Helper_Debug::msg("shippedOrderItemQty=$shippedOrderItemQty");
	        if($shippedOrderItemQty>$mageOrderQty) {
	          end_error("error shipped qty > order qty | sku($shipedOrderItemSku -> $newsku), qty($shippedOrderItemQty -> $mageOrderQty)", 423);
	        }
	        $shipItem=$convertOrder->itemToShipmentItem($mageOrderParentItemFound);
	        $shipItem->setQty($shippedOrderItemQty);
	        $shipment->addItem($shipItem);					
	      }
	      else {
	        $mageOrderQty=$mageOrderItemFound->getQtyToShip();
	        //Data_Icrc_Helper_Debug::msg("mageOrderQty=$mageOrderQtyParent mageOrderQty=$mageOrderQtyParent\n");
	        //Data_Icrc_Helper_Debug::msg("shippedOrderItemQty=$shippedOrderItemQty");
	        if($shippedOrderItemQty>$mageOrderQty) {
	          end_error("error shipped qty > order qty | sku($shipedOrderItemSku -> $newsku), qty($shippedOrderItemQty -> $mageOrderQty)", 423);
	        }
	        $shipItem=$convertOrder->itemToShipmentItem($mageOrderItemFound);
	        $shipItem->setQty($shippedOrderItemQty);
	        $shipment->addItem($shipItem);
	      }
	      $totalQty += $shippedOrderItemQty;
	      Data_Icrc_Helper_Debug::dump($shipment->getData());
	      unset($shipItem);
      }
    }
    if($isShipmentOk){
      if($totalQtyToShip>$totalQty)$isPartial=true;
      Data_Icrc_Helper_Debug::msg("totalQtyToShip=$totalQtyToShip totalQty=$totalQty\n");
      $shipment->setTotalQty($totalQty);
      $shipment->register();
      $shipment->getOrder()->setIsInProcess(true);
      $saveTransaction = Mage::getModel('core/resource_transaction')
	      ->addObject($shipment)
	      ->addObject($shipment->getOrder())
	      ->save();
      $mageOrder = Mage::getModel('sales/order')->loadByIncrementId($shippedOrderId);
      if($isPartial){
	      $mageOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, "processing_partial_shipment", 'Items partially shiped by logistic');
				$mageOrder->save();
      }
    }
    else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 422 Cannot process ship', true, 422);
      end_error("shipment ko: $shipmentError", 0);
    }
  }
  
}
Data_Icrc_Helper_Debug::msg("order processed");

die("OK\n");

