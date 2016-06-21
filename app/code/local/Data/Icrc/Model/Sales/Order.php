<?php

class Data_Icrc_Model_Sales_Order extends Mage_Sales_Model_Order
{
  public function canCreditmemo()
  {
    if ($this->hasForcedCanCreditmemo()) {
      return $this->getForcedCanCreditmemo();
    }

    if ($this->canUnhold() || $this->isPaymentReview()) {
      return false;
    }

    if ($this->isCanceled() || $this->getState() === self::STATE_CLOSED) {
      return false;
    }
    
    $allRefunded = true;
    foreach ($this->getAllItems() as $item) {
      if ($item->getQtyToRefund()) {
        $allRefunded = false;
        break;
      }
    }
    if ($allRefunded)
      return false;

    if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
      return false;
    }
    
    return true;
  }
  
  protected function _checkState()
  {
    parent::_checkState();
    if ($this->getState() == self::STATE_COMPLETE) {
      // Put order in CLOSED state if all the items are refunded and we're in COMPLETE state
      $allRefunded = true;
      //Data_Icrc_Helper_Debug::dump($this->getData());
      Data_Icrc_Helper_Debug::msg('begin refunded check');
      foreach ($this->getAllItems() as $item) {
        //Data_Icrc_Helper_Debug::dump($item);
        if ($item->getQtyInvoiced() > 0 && $item->getQtyToRefund()) {
          Data_Icrc_Helper_Debug::msg('allRefunded = false');
          $allRefunded = false;
          break;
        }
      }
      Data_Icrc_Helper_Debug::message('done refunded check: ' . ($allRefunded ? 'true' : 'false'));
      if ($allRefunded) {
        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;
        $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
      }
    }
    return $this;
  }
}

