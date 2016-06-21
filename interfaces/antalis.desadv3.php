<?php
include_once('app/Mage.php');
Mage::app();
/*echo "starting at ".date('c')."\n";
 $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
//$processes->walk('reindexAll');
foreach ($processes as $p) {
//Zend_Debug::dump($p);
//echo "*************************\n";
//echo $p->getIndexerCode()."\n";
}
$indexer = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
$indexer->reindexAll();
echo "done at ".date('c')."\n";
*/


/*if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed', true, 405);
	die("You must POST on this URL\n");
}*/

//$xml = file_get_contents('php://input');
$xml = file_get_contents('/var/www/magento/interfaces/test.xml');
$doc = new DOMDocument;
$ret = $doc->loadXML($xml);

if ($ret == false) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
	die("Cannot parse XML\n");
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
	if(!is_null($elements)) {
		foreach($elements as $element){
			$node = $element->getElementsByTagName('MATNR');
			if(!is_null($node)){
				$matnr=$node->item(0)->nodeValue;
			}
			$node = $element->getElementsByTagName('VSART');
			if(!is_null($node)){
				$vsart=$node->item(0)->nodeValue;
			}
			$node = $element->getElementsByTagName('LFIMG');
			if(!is_null($node)){
				$lfimg=$node->item(0)->nodeValue;
			}
			$node = $element->getElementsByTagName('BSTNR');
			if(!is_null($node)){
				$bstnr=$node->item(0)->nodeValue;
			}
			$node = $element->getElementsByTagName('POSEX');
			if(!is_null($node)){
				$posex=$node->item(0)->nodeValue;
			}
			if(!is_null($matnr) && !is_null($lfimg) && !is_null($bstnr)) {
				$orderId=substr($bstnr,1);
				if(!array_key_exists($orderId,$shippedOrders))
				{
					$shippedOrders[$orderId]=array();
				}
				$shippedOrders[$orderId]=array_merge($shippedOrders[$orderId],array($matnr => $lfimg));
			}
			Data_Icrc_Helper_Debug::msg("produit trouvé : $matnr quatité : $lfimg");
		}
	}

  Data_Icrc_Helper_Debug::dump($shippedOrders);

	foreach($shippedOrders as $shippedOrderId => $shippedOrderItems){
		$isPartial=false;
		$mageOrder = Mage::getModel('sales/order')->loadByIncrementId($shippedOrderId);
		if (!$mageOrder->getId()) {
		  header($_SERVER['SERVER_PROTOCOL'] . ' 420 Order not found', true, 420);
		  die("Order $shippedOrderId not found");
		}
		if (!$mageOrder->canShip()) {
		  header($_SERVER['SERVER_PROTOCOL'] . ' 421 Cannot ship', true, 421);
		  die("Cannot do shipment for order $shippedOrderId");
		}
		$convertOrder = new Mage_Sales_Model_Convert_Order();
		$shipment = $convertOrder->toShipment($mageOrder);
		$mageOrderItems = $mageOrder->getAllItems();

		$totalQtyToShip=0;
		foreach ($mageOrderItems as $mageOrderItem) {
           	$totalQtyToShip+=$mageOrderItem->getQtyToShip();
        }
		echo "mageOrder item count =".count($mageOrderItems);
		$totalQty = 0;
		$isShipmentOk=true;
		foreach($shippedOrderItems as $shipedOrderItemSku => $shippedOrderItemQty){
			$shippedProductId=Mage::getModel('catalog/product')->getIdBySku($shipedOrderItemSku);
			echo "shippedProductId = $shippedProductId";
			$mageOrderItemFound=null;
			foreach ($mageOrderItems as $mageOrderItem) {
            	if ($mageOrderItem->getProductId()==$shippedProductId) {
                	$mageOrderItemFound=$mageOrderItem;
            		break;
            	}
        	}
        	//$mageOrderItem=$mageOrder->getItemById($shippedOrderItemId);
			if(!$mageOrderItemFound){
				$isShipmentOk=false;
				echo "shipment ko";
				break;
			}
			else {
				$mageOrderShippedItemParentId=$mageOrderItemFound->getParentItemId();
				echo "mageOrderShippedItemParentId=$mageOrderShippedItemParentId\n";
				$mageOrderParentItemFound=null;
				if(!is_null($mageOrderShippedItemParentId)){
					$mageOrderParentItemFound=$mageOrder->getItemById($mageOrderShippedItemParentId);
				}
				
				if($mageOrderParentItemFound){
					$mageOrderQty=$mageOrderParentItemFound->getQtyToShip();
					echo "mageOrderQtyToShip=$mageOrderQty\n";
					//echo "shippedOrderItemQty=$shippedOrderItemQty";
					if($shippedOrderItemQty>$mageOrderQty) die ("error shipped qty > order qty");
					$shipItem=$convertOrder->itemToShipmentItem($mageOrderParentItemFound);
					$shipItem->setQty($shippedOrderItemQty);
					$shipment->addItem($shipItem);					
				}
				else {
					$mageOrderQty=$mageOrderItemFound->getQtyToShip();
					//echo "mageOrderQty=$mageOrderQtyParent mageOrderQty=$mageOrderQtyParent\n";
					//echo "shippedOrderItemQty=$shippedOrderItemQty";
					if($shippedOrderItemQty>$mageOrderQty) die ("error shipped qty > order qty");
					$shipItem=$convertOrder->itemToShipmentItem($mageOrderItemFound);
					$shipItem->setQty($shippedOrderItemQty);
					$shipment->addItem($shipItem);
				}
				$totalQty += $shippedOrderItemQty;
				var_dump($shipment->getData());
				unset($shipItem);
			}
		}
		if($isShipmentOk){
			if($totalQtyToShip>$totalQty)$isPartial=true;
			echo "totalQtyToShip=$totalQtyToShip totalQty=$totalQty\n";
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
	}

	/*$i = 0;
	 $client = $_SERVER['REMOTE_ADDR'];
	$date = date(DateTime::ATOM);
	$filename = "/tmp/input-xml-${client}-${date}.xml";
	while (file_exists($filename)) {
	$i++;
	$filename = "/tmp/input-xml-${client}-${date}-${i}.xml";
	}
	$doc->save($filename);*/
}
die("OK\n");

