<?php
/**
 * Rewrite for custom attribute
 * Original: Sales Order Invoice PDF model
 */
class Data_Icrc_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
  protected function getInternalId() {
    return Mage::helper('data_icrc/internal')->getInternalId();
  }

  protected function isInternal($order) {
    return Mage::helper('data_icrc/internal')->isInternal($order->getStoreId());
  }

  /**
   * Insert order to pdf page
   *
   * @param Zend_Pdf_Page $page
   * @param Mage_Sales_Model_Order $obj
   * @param bool $putOrderId
   */
  protected function insertOrder(&$page, $obj, $putOrderId = true)
  {
    if ($obj instanceof Mage_Sales_Model_Order) {
      $shipment = null;
      $order = $obj;
    } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
      $shipment = $obj;
      $order = $shipment->getOrder();
    }

    $this->y = $this->y ? $this->y : 815;
    $top = $this->y;

    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
    $page->drawRectangle(25, $top, 570, $top - 55);
    $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
    $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
    $this->_setFontRegular($page, 10);

    if ($putOrderId) {
      $page->drawText(
        Mage::helper('sales')->__('Order # ') . $order->getRealOrderId(), 35, ($top -= 30), 'UTF-8'
      );
    }
    $page->drawText(
        Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
            $order->getCreatedAtStoreDate(), 'medium', false
        ),
        35,
        ($top -= 15),
        'UTF-8'
    );

    $top -= 10;
    $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
    $page->setLineWidth(0.5);
    $page->drawRectangle(25, $top, 275, ($top - 25));
    $page->drawRectangle(275, $top, 570, ($top - 25));

    /* Calculate blocks info */

    /* Billing Address */
    if ($this->isInternal($order))
      $billingAddress = $this->_formatAddress(Mage::helper('data_icrc/internal')->getBillingInfoPdf($order->getQuoteId()));
    else
      $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

    /* Payment */
    $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
        ->setIsSecureMode(true)
        ->toPdf();
    $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
    $payment = explode('{{pdf_row_separator}}', $paymentInfo);
    foreach ($payment as $key=>$value){
      if (strip_tags(trim($value)) == '') {
        unset($payment[$key]);
      }
    }
    reset($payment);

    /* Shipping Address and Method */
    if (!$order->getIsVirtual()) {
      /* Shipping Address */
      if ($this->isInternal($order))
        $shippingAddress = $this->_formatAddress(Mage::helper('data_icrc/internal')->getShippingInfoPdf($order->getQuoteId()));
      else
        $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
        $shippingMethod  = $order->getShippingDescription();
        $shippingMethod  = html_entity_decode($shippingMethod, ENT_QUOTES);
    }

    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $this->_setFontBold($page, 12);
    $page->drawText(Mage::helper('sales')->__('Sold to:'), 35, ($top - 15), 'UTF-8');

    if (!$order->getIsVirtual()) {
      $page->drawText(Mage::helper('sales')->__('Ship to:'), 285, ($top - 15), 'UTF-8');
    } else {
      $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, ($top - 15), 'UTF-8');
    }

    $addressesHeight = $this->_calcAddressHeight($billingAddress);
    if (isset($shippingAddress)) {
      $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
    }

    $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
    $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $this->_setFontRegular($page, 10);
    $this->y = $top - 40;
    $addressesStartY = $this->y;

    foreach ($billingAddress as $value){
      if ($value !== '') {
        $text = array();
        foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
          $text[] = $_value;
        }
        foreach ($text as $part) {
          $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
          $this->y -= 15;
        }
      }
    }

    $addressesEndY = $this->y;

    if (!$order->getIsVirtual()) {
      $this->y = $addressesStartY;
      foreach ($shippingAddress as $value){
        if ($value!=='') {
          $text = array();
          foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
            $text[] = $_value;
          }
          foreach ($text as $part) {
            $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
            $this->y -= 15;
          }
        }
      }

      $addressesEndY = min($addressesEndY, $this->y);
      $this->y = $addressesEndY;

      $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
      $page->setLineWidth(0.5);
      $page->drawRectangle(25, $this->y, 275, $this->y-25);
      $page->drawRectangle(275, $this->y, 570, $this->y-25);

      $this->y -= 15;
      $this->_setFontBold($page, 12);
      $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
      $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
      $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

      $this->y -=10;
      $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

      $this->_setFontRegular($page, 10);
      $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

      $paymentLeft = 35;
      $yPayments   = $this->y - 15;
    }
    else {
      $yPayments   = $addressesStartY;
      $paymentLeft = 285;
    }

    foreach ($payment as $value){
      if (trim($value) != '') {
        //Printing "Payment Method" lines
        $value = preg_replace('/<br[^>]*>/i', "\n", $value);
        foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
          $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
          $yPayments -= 15;
        }
      }
    }

    if ($order->getIsVirtual()) {
      // replacement of Shipments-Payments rectangle block
      $yPayments = min($addressesEndY, $yPayments);
      $page->drawLine(25,  ($top - 25), 25,  $yPayments);
      $page->drawLine(570, ($top - 25), 570, $yPayments);
      $page->drawLine(25,  $yPayments,  570, $yPayments);

      $this->y = $yPayments - 15;
    } else {
      $topMargin    = 15;
      $methodStartY = $this->y;
      $this->y     -= 15;

      foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
        $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
        $this->y -= 15;
      }

      $yShipments = $this->y;
        $pos = strpos($order->formatPriceTxt($order->getShippingAmount()), 'Fr.');
        if($pos === false) {
            $shippingamount = $order->formatPriceTxt($order->getShippingAmount());
        } else {
            $shippingamount = str_replace('Fr.', '', $order->formatPriceTxt($order->getShippingAmount())). ' CHF';
        }
      $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " "
          . $shippingamount . ")";

      $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
      $yShipments -= $topMargin + 10;

      $tracks = array();
      if ($shipment) {
        $tracks = $shipment->getAllTracks();
      }
      if (count($tracks)) {
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
        $page->drawLine(400, $yShipments, 400, $yShipments - 10);
        //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

        $this->_setFontRegular($page, 9);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Number'), 410, $yShipments - 7, 'UTF-8');

        $yShipments -= 20;
        $this->_setFontRegular($page, 8);
        foreach ($tracks as $track) {
          $CarrierCode = $track->getCarrierCode();
          if ($CarrierCode != 'custom') {
            $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
            $carrierTitle = $carrier->getConfigData('title');
          } else {
            $carrierTitle = Mage::helper('sales')->__('Custom Value');
          }

          //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
          $maxTitleLen = 45;
          $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
          $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
          //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
          $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
          $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
          $yShipments -= $topMargin - 5;
        }
      } else {
        $yShipments -= $topMargin - 5;
      }

      $currentY = min($yPayments, $yShipments);

      // replacement of Shipments-Payments rectangle block
      $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
      $page->drawLine(25,  $currentY,     570, $currentY); //bottom
      $page->drawLine(570, $currentY,     570, $methodStartY); //right

      $this->y = $currentY;
      $this->y -= 15;
    }
  }

  public function getPdf($invoices = array())
  {
    $this->_beforeGetPdf();
    $this->_initRenderer('invoice');

    $pdf = new Zend_Pdf();
    $this->_setPdf($pdf);
    $style = new Zend_Pdf_Style();
    $this->_setFontBold($style, 10);

    foreach ($invoices as $invoice) {
      if ($invoice->getStoreId()) {
        Mage::app()->getLocale()->emulate($invoice->getStoreId());
        Mage::app()->setCurrentStore($invoice->getStoreId());
      }
      $page  = $this->newPage();
      $order = $invoice->getOrder();
      /* Add image */
      $this->insertLogo($page, $invoice->getStore());
      /* Add address */
      $this->insertAddress($page, $invoice->getStore());
      /* Add head */
      $this->insertOrder(
        $page,
        $order,
        Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
      );
      /* Add document text and number */
      $this->insertDocumentNumber(
        $page,
        Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
      );
      /* Add table */
      $this->_drawHeader($page);
      /* Add body */
      $donations = array();
      foreach ($invoice->getAllItems() as $item){
        if ($item->getOrderItem()->getParentItem()) {
          continue;
        }
        if (strncmp($item->getOrderItem()->getSku(), 'donation-', 9) == 0) {
          $donations[] = $item;
          $this->setTotalsOnlySubtotal(1);
          continue;
        }
        /* Draw item */
        $this->_drawItem($item, $page, $order);
        $page = end($pdf->pages);
      }
      /* Add totals */
      $this->insertTotals($page, $invoice);

      /* Donations */
      if (count($donations)) {
        $this->_drawHeader($page, 'Donations');
        foreach ($donations as $item) {
          /* Draw item */
          $this->_drawItem($item, $page, $order);
          $page = end($pdf->pages);
        }
        /* Add totals */
        $this->setTotalsOnlySubtotal(0);
        $this->setTotalsAddDonationsSubtotal(1);
        $this->insertTotals($page, $invoice);
      }

      if ($invoice->getStoreId()) {
        Mage::app()->getLocale()->revert();
      }
    }
    $this->_afterGetPdf();
    return $pdf;
  }
  
    /**
    * OVERRIDE
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
    protected function insertTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );
        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    if(isset($totalData['label'])) {
                        $lineBlock['lines'][] = array(
                            array(
                                'text'      => $totalData['label'],
                                'feed'      => 475,
                                'align'     => 'right',
                                'font_size' => $totalData['font_size'],
                                'font'      => 'bold'
                            ),
                            array(
                                'text'      => $totalData['amount'],
                                'feed'      => 565,
                                'align'     => 'right',
                                'font_size' => $totalData['font_size'],
                                'font'      => 'bold'
                            ),
                        );
                    } else {
                        foreach($totalData as $tot) {
                            $lineBlock['lines'][] = array(
                                array(
                                    'text'      => $tot['label'],
                                    'feed'      => 475,
                                    'align'     => 'right',
                                    'font_size' => $tot['font_size'],
                                    'font'      => 'bold'
                                ),
                                array(
                                    'text'      => $tot['amount'],
                                    'feed'      => 565,
                                    'align'     => 'right',
                                    'font_size' => $tot['font_size'],
                                    'font'      => 'bold'
                                ),
                            );
                        }
                    }
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }
    /**
     * OVERRIDE
     * @param Zend_Pdf_Page $page
     * @param type $title
     */
  protected function _drawHeader(Zend_Pdf_Page $page, $title = 'Products')
  {
    /* Add table head */
    $this->_setFontRegular($page, 10);
    $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
    $page->setLineWidth(0.5);
    $page->drawRectangle(25, $this->y, 570, $this->y -15);
    $this->y -= 10;
    $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

    //columns headers
    $lines[0][] = array(
        'text' => Mage::helper('sales')->__($title),
        'feed' => 35
    );

    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('SKU'),
        'feed'  => 245,
        'align' => 'center'
    );

    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('Price'),
        'feed'  => 320,//360
        'align' => 'right'
    );

    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('Qty'),
        'feed'  => 390,//435
        'align' => 'right'
    );

    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('V.A.T.'),
        'feed'  => 440,//495
        'align' => 'right'
    );
    
    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('V.A.T. rate'),
        'feed'  => 500,//535
        'align' => 'right'
    );

    $lines[0][] = array(
        'text'  => Mage::helper('sales')->__('Subtotal'),
        'feed'  => 560,
        'align' => 'right'
    );

    $lineBlock = array(
        'lines'  => $lines,
        'height' => 5
    );

    $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
    $this->y -= 20;
  }
  
  protected function _getTotalsList($source)
  {
    if ($this->getTotalsOnlySubtotal()) {
      $totals = array(
        0 => array('@' => array('translate' => 'title'),
            'title' => Mage::helper('sales')->__('Subtotal HT'),
            'source_field' => 'subtotal',
            'font_size' => '7',
            'display_zero' => '1',
            'sort_order' => '200',
            'model' => 'data_icrc/order_pdf_subtotal_nodonations'
        ),
        1 => array('@' => array('translate' => 'title'),
            'title' => 'Discount',
            'source_field' => 'discount_amount',
            'amount_prefix' => '-',
            'font_size' => '7',
            'display_zero' => '0',
            'sort_order' => '100')
      );
    }
    else {
      $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
    }
    usort($totals, array($this, '_sortTotalsList'));
    $totalModels = array();
    foreach ($totals as $index => $totalInfo) {
      if (!empty($totalInfo['model'])) {
        $totalModel = Mage::getModel($totalInfo['model']);
        if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
          $totalInfo['model'] = $totalModel;
        } else {
          Mage::throwException(
            Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
          );
        }
      } else {
        $totalModel = Mage::getModel($this->_defaultTotalModel);
      }
      if($totalInfo['source_field'] == 'subtotal') {
        if ($this->setTotalsAddDonationsSubtotal()) {
            // Replace first by our
            $totalInfo['model'] = 'data_icrc/order_pdf_subtotal_donations';
            $totalInfo['title'] = Mage::helper('sales')->__('Subtotal HT');
            $totalInfo['sort_order'] = 200;
        }
      }
      if((($this->getTotalsAddDonationsSubtotal() == 0) && ($totalInfo['source_field'] == 'discount_amount')) || ($totalInfo['source_field'] != 'discount_amount')) {
        $totalModel->setData($totalInfo);
        $totalModels[] = $totalModel;
      }
    }
    return $totalModels;
  }

  protected function insertLogo(&$page, $store = null)
  {
    $this->y = $this->y ? $this->y : 815;
    $image = Mage::getStoreConfig('sales/identity/logo', $store);
    if ($image) {
      $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
      if (is_file($image)) {
        $image       = Zend_Pdf_Image::imageWithPath($image);
        $top         = 830; //top border of the page
        $widthLimit  = Mage::getStoreConfig('icrc/output/pdf_logo_max_width', $store);
        $heightLimit = Mage::getStoreConfig('icrc/output/pdf_logo_max_height', $store);
        $width       = $image->getPixelWidth();
        $height      = $image->getPixelHeight();

        //preserving aspect ratio (proportions)
        $ratio = $width / $height;
        if ($ratio > 1 && $width > $widthLimit) {
            $width  = $widthLimit;
            $height = $width / $ratio;
        } elseif ($ratio < 1 && $height > $heightLimit) {
            $height = $heightLimit;
            $width  = $height * $ratio;
        } elseif ($ratio == 1 && $height > $heightLimit) {
            $height = $heightLimit;
            $width  = $widthLimit;
        }

        $y1 = $top - $height;
        $y2 = $top;
        $x1 = 25;
        $x2 = $x1 + $width;

        //coordinates after transformation are rounded by Zend
        $page->drawImage($image, $x1, $y1, $x2, $y2);

        $this->y = $y1 - 10;
      }
    }
  }
}

