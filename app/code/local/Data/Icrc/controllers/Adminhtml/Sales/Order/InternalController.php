<?php

class Data_Icrc_Adminhtml_Sales_Order_InternalController extends Mage_Adminhtml_Controller_Action
{
  public function sendmanagerAction()
  {
    $id = $this->getRequest()->getParam('orderid');
    $pending = Mage::getModel('data_icrc/payment_pending');
    $pending->load($id, 'order_id');
    if ($pending->getId()) {
      $pending->sendEmail(null);// $customer parameter is unused
      $this->_getSession()->addSuccess($this->__('The order email has been sent.'));
    }
        
    $this->_redirect('*/sales_order/view', array('order_id' => $id));
  }
}

