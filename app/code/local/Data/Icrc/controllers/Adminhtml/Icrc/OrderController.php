<?php

class Data_Icrc_Adminhtml_Icrc_OrderController extends Mage_Adminhtml_Controller_Action
{
  const STATE_VALIDATED = 'processing_validated';
  const STATE_TOLOGISTIC = 'processing_sent_to_logistic';

  protected function _isAllowed()
  {
    return Mage::getSingleton('admin/session')->isAllowed('data_icrc');
  }

  public function massValidateAction()
  {
    $validatedCount = 0;
    $nonValidatedCount = 0;
    foreach ($this->getRequest()->getParam('order_ids') as $id) {
      $order = Mage::getModel('sales/order')->load($id);
      $state = $order->getState();
      $status = $order->getStatus();
//error_log("state: $state, status: $status");
      if ($state == Mage_Sales_Model_Order::STATE_PROCESSING &&
          ($status == Mage_Sales_Model_Order::STATE_PROCESSING ||
           $status == self::STATE_VALIDATED)) {
          $order->setStatus(self::STATE_VALIDATED, true);
          if ($order->save())
            $validatedCount++;
          else
            $nonValidatedCount++;
      }
      else
          $nonValidatedCount++;
    }

    if ($nonValidatedCount) {
      if ($validatedCount) {
        Mage::getSingleton('core/session')->addError($this->__('%s order(s) cannot be validated', $nonValidatedCount));
      } else {
        Mage::getSingleton('core/session')->addError($this->__('The order(s) cannot be validated'));
      }
    }
    if ($validatedCount) {
      Mage::getSingleton('core/session')->addSuccess($this->__('%s order(s) have been validated.', $validatedCount));
    }

    $this->_redirect('*/sales_order/');
    //Mage::getSingleton('core/session')->addNotice(Zend_Debug::dump(Mage_Sales_Model_Order::STATE_PROCESSING));
    //$this->loadLayout();
    //$this->renderLayout();
  }
}

