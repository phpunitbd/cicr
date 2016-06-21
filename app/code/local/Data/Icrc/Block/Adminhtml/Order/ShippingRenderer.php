<?php

class Data_Icrc_Block_Adminhtml_Order_ShippingRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $helper = $this->helper('data_icrc/internal');
    if ($helper->isInternal($row->getData('store_id'))) {
      return '<strong>Unit or Delegation</strong>: ' . $row->getData('shipping_unit');
    }
    return $row->getData($this->getColumn()->getIndex());
  }
}
