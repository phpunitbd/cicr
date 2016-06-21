<?php

class Data_Icrc_Model_Tax_Sales_Pdf_Tax extends Mage_Tax_Model_Sales_Pdf_Tax
{
    /**
     * OVERRIDE
     * Check if tax amount should be included to grandtotal block
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
        $config= Mage::getSingleton('tax/config');
        if ($config->displaySalesTaxWithGrandTotal($store)) {
            return array();
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = array();

        if ($config->displaySalesFullSummary($store)) {
           $totals = $this->getFullTaxInfo();
        }
        
        return $totals;
    }
    
    /**
     * OVERRIDE
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getFullTaxInfo()
    {
        $taxClassAmount = Mage::helper('tax')->getCalculatedTaxes($this->getOrder());
        $fontSize       = $this->getFontSize() ? $this->getFontSize() : 7;

        if (!empty($taxClassAmount)) {
            $shippingTax    = Mage::helper('tax')->getShippingTax($this->getOrder());
            $taxClassAmount = array_merge($shippingTax, $taxClassAmount);

            foreach ($taxClassAmount as &$tax) {
                $percent          = $tax['percent'] ? ' ' . (float)$tax['percent']. '%' : '';
                $pos = strpos($this->getAmountPrefix().$this->getOrder()->formatPriceTxt($tax['tax_amount']), 'Fr.');
                if($pos === false) {
                    $tax['amount']    = $this->getAmountPrefix().$this->getOrder()->formatPriceTxt($tax['tax_amount']);
                } else {
                    $tax['amount']    = str_replace('Fr.', '', $this->getAmountPrefix().$this->getOrder()->formatPriceTxt($tax['tax_amount'])). ' CHF';
                }
                    
                $tax['label']     = Mage::helper('tax')->__('V.A.T.') . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            $rates    = Mage::getResourceModel('sales/order_tax_collection')->loadByOrder($this->getOrder())->toArray();
            $fullInfo = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
            $tax_info = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }
                    
                    $pos = strpos($this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount), 'Fr.');
                    if($pos === false) {
                        $_amount = $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount);
                    } else {
                        $_amount = str_replace('Fr.', '', $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount)). ' CHF';
                    }

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' ' . (float)$rate['percent']. '%' : '';

                        $tax_info[] = array(
                            'amount'    => $_amount,
                            'label'     => Mage::helper('tax')->__('V.A.T.') . $percent . ':',
                            'font_size' => $fontSize
                        );
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }
}