<?php

class Data_Icrc_Block_Adminhtml_Order_BillingRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $helper = $this->helper('data_icrc/internal');
    if ($helper->isInternal($row->getData('store_id'))) {
      $value = '<ul>';

      $value .= '<li><strong>' . Data_Icrc_Helper_Attributes::BILLING_UNIT . '</strong>: ' . $row->getData('billing_unit') . '<li>';
      $value .= '<li><strong>' . Data_Icrc_Helper_Attributes::BILLING_COST_CENTER . '</strong>: ' . $row->getData('billing_cc') . '<li>';
      $value .= '<li><strong>' . Data_Icrc_Helper_Attributes::BILLING_OBJECTIVE_CODE . '</strong>: ' . $row->getData('billing_oc') . '<li>';

      return $value . '</ul>';
    }
    $value = $row->getData($this->getColumn()->getIndex());
    $customer_type = $this->helper('data_icrc/attributes')->getLabel($row->getData('client_type'));
    return $value.'<br/>'.$customer_type;
  }
}
