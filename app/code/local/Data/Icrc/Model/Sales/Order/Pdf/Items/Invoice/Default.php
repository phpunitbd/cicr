<?php

class Data_Icrc_Model_Sales_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
  /**
  * Get store label if possible
  */
  public function getItemOptions() {
    $result = parent::getItemOptions();
    $_product = Mage::getModel('catalog/product')->load($this->getItem()->getOrderItem()->getProductId());
    if (!$_product->isConfigurable())
      return $_product->getOptions();
    $_inst = $_product->getTypeInstance(true);
    $attrs  = $_inst->getConfigurableAttributesAsArray($_product);
    foreach ($result as &$opt) {
      foreach ($attrs as $attr) {
        if ($attr['label'] == $opt['label']) {
          $opt['label'] = $attr['store_label'];
          break;
        }
      }
    }
    return $result;
  }
  
    /**
     * OVERRIDE
     * Draw item line
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
            'feed'  => 260,
            'align' => 'right'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => 390,
            'align' => 'right'
        );

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 340;
        $feedSubtotal = $feedPrice + 220;
        foreach ($prices as $priceData){
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedPrice,
                    'align' => 'right'
                );
                // draw Subtotal label
                $lines[$i][] = array(
                    'text'  => $priceData['label'],
                    'feed'  => $feedSubtotal,
                    'align' => 'right'
                );
                $i++;
            }
            // draw Price
            $posprice = strpos($priceData['price'], 'Fr.');
            if($posprice === false) {
                $priceamount = $priceData['price'];
            } else {
                $priceamount = str_replace('Fr.', '', $priceData['price']). ' CHF';
            }
            $lines[$i][] = array(
                'text'  => $priceamount,
                'feed'  => $feedPrice,
                'font'  => 'bold',
                'align' => 'right'
            );
            // draw Subtotal
            $possubtotal = strpos($priceData['subtotal'], 'Fr.');
            if($possubtotal === false) {
                $subtotalamount = $priceData['subtotal'];
            } else {
                $subtotalamount = str_replace('Fr.', '', $priceData['subtotal']). ' CHF';
            }
            $lines[$i][] = array(
                'text'  => $subtotalamount,
                'feed'  => $feedSubtotal,
                'font'  => 'bold',
                'align' => 'right'
            );
            $i++;
        }

        // draw Tax
        $postax = strpos($order->formatPriceTxt($item->getTaxAmount()), 'Fr.');
        if($postax === false) {
                $taxamount = $order->formatPriceTxt($item->getTaxAmount());
        } else {
            $taxamount = str_replace('Fr.', '', $order->formatPriceTxt($item->getTaxAmount())). ' CHF';
        }
        $lines[0][] = array(
            'text'  => $taxamount,
            'feed'  => 440,
            'font'  => 'bold',
            'align' => 'right'
        );
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $productsPrice = floatval($product->getData("price")); 
  
        $taxClassId = $product->getData("tax_class_id");
        $taxRate = 0;
        if($item->getTaxAmount() > 0) {
            if($taxClassId != 0) {
                $taxClasses  = Mage::helper("core")->jsonDecode( Mage::helper("tax")->getAllRatesByProductClass());
                $taxRate   = $taxClasses["value_".$taxClassId];
                $taxRate   = $taxClasses["value_".$taxClassId];
            }
            if($taxRate == ''){
                $taxRate = 0;
            }
        }
        
        $lines[0][] = array(
            'text'  => $taxRate. '%',
            'feed'  => 480,
            'font'  => 'bold',
            'align' => 'right'
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 20
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}

