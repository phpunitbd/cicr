<?php

class Data_Icrc_Model_Sales_Order_Pdf_Total_Default extends Mage_Sales_Model_Order_Pdf_Total_Default
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
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $pos = strpos($amount, 'Fr.');
        if($pos === false) {
            $amount = $amount;
        } else {
            $amount = str_replace('Fr.', '', $amount). ' CHF';
        }
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix().$amount;
        }
        $label = Mage::helper('sales')->__($this->getTitle()) . ':';
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }
}
