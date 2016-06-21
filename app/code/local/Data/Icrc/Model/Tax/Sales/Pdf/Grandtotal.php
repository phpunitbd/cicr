<?php

class Data_Icrc_Model_Tax_Sales_Pdf_Grandtotal extends Mage_Tax_Model_Sales_Pdf_Grandtotal
{
  public function getTotalsForDisplay()
  {
        $order = $this->getOrder();
        $store = $order->getStore();
        $config= Mage::getSingleton('tax/config');
        if (!$config->displaySalesTaxWithGrandTotal($store)) {
            $amount = $order->formatPriceTxt($this->getAmount());
            if ($this->getAmountPrefix()) {
                $amount = $this->getAmountPrefix().$amount;
            }
            $label = Mage::helper('sales')->__('Grand Total TTC') . ':';
            $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
            $pos = strpos($amount, 'Fr.');
            if($pos === false) {
                $grandtotal = $amount;
            } else {
                $grandtotal = str_replace('Fr.', '', $amount). ' CHF';
            }
            $total[] = array(
                'amount'    => $grandtotal,
                'label'     => $label,
                'font_size' => $fontSize
            );
            if ($order->getOrderCurrencyCode() != $order->getStoreCurrencyCode()) {
              $total[] = array(
                'amount'    => $this->getAmountPrefix().$order->getBaseCurrency()->formatTxt($order->getBaseGrandTotal()),
                'label'     => Mage::helper('sales')->__('Grand Total to be Charged') . ':',
                'font_size' => $fontSize
              );
            }
            return array($total);
        }
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $this->getSource()->getTaxAmount();
        $amountExclTax = ($amountExclTax > 0) ? $amountExclTax : 0;
        $amountExclTax = $this->getOrder()->formatPriceTxt($amountExclTax);
        $tax = $this->getOrder()->formatPriceTxt($this->getSource()->getTaxAmount());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = array(array(
            'amount'    => $this->getAmountPrefix().$amountExclTax,
            'label'     => Mage::helper('tax')->__('Grand Total (Excl. Tax)') . ':',
            'font_size' => $fontSize
        ));

        if ($config->displaySalesFullSummary($store)) {
           $totals = array_merge($totals, $this->getFullTaxInfo());
        }

        $totals[] = array(
            'amount'    => $this->getAmountPrefix().$tax,
            'label'     => Mage::helper('tax')->__('Tax') . ':',
            'font_size' => $fontSize
        );
        $totals[] = array(
            'amount'    => $this->getAmountPrefix().$amount,
            'label'     => Mage::helper('tax')->__('Grand Total (Incl. Tax)') . ':',
            'font_size' => $fontSize
        );
    if ($order->getOrderCurrencyCode() != $order->getStoreCurrencyCode()) {
      $totals[] = array(
        'amount'    => $this->getAmountPrefix().$order->getBaseCurrency()->formatTxt($order->getBaseGrandTotal()),
        'label'     => Mage::helper('sales')->__('Grand Total to be Charged') . ':',
        'font_size' => $fontSize
      );
    }
    return $totals;
  }
}

