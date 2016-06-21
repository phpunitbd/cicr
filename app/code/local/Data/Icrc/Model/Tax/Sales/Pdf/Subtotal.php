<?php
class Data_Icrc_Model_Tax_Sales_Pdf_Subtotal extends Mage_Tax_Model_Sales_Pdf_Subtotal
{
    /**
     * OVERRIDE
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $helper= Mage::helper('tax');
        $amount = $this->getAmount() - $this->getSource()->getDiscountAmount();
        $amount = $this->getOrder()->formatPriceTxt($amount);
        if ($this->getSource()->getSubtotalInclTax()) {
            $amountInclTax = $this->getSource()->getSubtotalInclTax();
        } else {
            $amountInclTax = $this->getAmount()
                +$this->getSource()->getTaxAmount()
                -$this->getSource()->getShippingTaxAmount();
        }
        
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        
        if ($helper->displaySalesSubtotalBoth($store)) {
            $totals = array(
                array(
                    'amount'    => $this->getAmountPrefix().$amount,
                    'label'     => Mage::helper('sales')->__('Subtotal excl Tax') . ':',
                    'font_size' => $fontSize
                ),
                array(
                    'amount'    => $this->getAmountPrefix().$amountInclTax,
                    'label'     => Mage::helper('sales')->__('Subtotal excl Tax') . ':',
                    'font_size' => $fontSize
                ),
            );
        } elseif ($helper->displaySalesSubtotalInclTax($store)) {
            $totals = array(array(
                'amount'    => $this->getAmountPrefix().$amountInclTax,
                'label'     => Mage::helper('sales')->__($this->getTitle()) . ':',
                'font_size' => $fontSize
            ));
        } else {
            $pos = strpos($this->getAmountPrefix().$amount, 'Fr.');
            if($pos === false) {
                $subtotal = $this->getAmountPrefix().$amount;
            } else {
                $subtotal = str_replace('Fr.', '', $this->getAmountPrefix().$amount). ' CHF';
            }
            $totals = array(array(
                'amount'    => $subtotal,
                'label'     => Mage::helper('sales')->__('Subtotal excl Tax') . ':',
                'font_size' => $fontSize
            ));
        }
        
        return $totals;
    }
}