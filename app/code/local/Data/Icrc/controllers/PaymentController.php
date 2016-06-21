<?php

class Data_Icrc_PaymentController extends Mage_Core_Controller_Front_Action
{
  public function acceptAction() {
    $pending = $this->_getPending();
    if ($pending) {
      $lock = $this->_lock($pending);
      $this->_processPayment($pending);
      $this->_unlock($lock);
    }
    $this->_redirect('*/*/message');
  }

  public function refuseAction() {
    $pending = $this->_getPending();
    if ($pending) {
      $lock = $this->_lock($pending);
      $this->_abortPayment($pending);
      $this->_unlock($lock);
    }
    $this->_redirect('*/*/message');
  }

/*
  public function showAction() {
    $c = Mage::getModel('data_icrc/payment_pending')->getCollection();
    echo '<ul>';
    foreach ($c as $p) {
      echo '<li>' . $p->getId() . ': ';
      try {
        echo '<a href="' . $p->getUrl(true) . 
             '">validate</a> or <a href="' . 
             $p->getUrl(false) . '">refuse</a>';
      }
      catch (Exception $e) {
      }
      echo '</li>';
    }
    echo '</ul>';
  }
*/

  public function messageAction() {
    if (!$this->_checkInternal())
      return;
    $this->loadLayout(array('default'));
    $this->_initLayoutMessages('customer/session');
    $this->renderLayout();
  }

  protected function _getPending() {
    if (!$this->_checkInternal())
      return null;
    $id = $this->getRequest()->getParam('id', '');
    $pending = Mage::getModel('data_icrc/payment_pending')->load($id);
    if ($pending->getId() < 1) {
      Mage::getSingleton('core/session')->addNotice('No such order, or order already processed');
      return null;
    }
    $token = $this->getRequest()->getParam('token', '');
    if ($pending->getToken() != $token) {
      Mage::getSingleton('core/session')->addError('invalid token');
      return null;
    }
    return $pending;
  }

  protected function _checkInternal() {
    if (Mage::app()->getStore()->getCode() == 'internal')
      return true;
    Mage::getSingleton('core/session')->addError('Forbidden out of internal store');
    $this->_redirect('/');
    return false;
  }

  protected function _abortPayment($pending) {
    /** @var $_session Mage_Core_Model_Session */
    $_session = Mage::getSingleton('core/session');
    try {
      /** @var $order Mage_Sales_Model_Order */
      $order = Mage::getModel('sales/order');
      $order->load($pending->getOrderId());
      if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
        $order->cancel();
        /** @var $payment Mage_Sales_Model_Order_Payment */
        $payment = $order->getPayment();
        $payment->setStatus(Data_Icrc_Model_InternalPaymentMethod::STATUS_REJECTED)
          ->setIsTransactionClosed(1);
        $order->setState('canceled', true, 'Canceled by ' . $pending->getEmail())
          ->save();
        $_session->addSuccess('Order canceled');
        $pending->delete();
        $this->_sendPaymentFailedEmail($order, $this->__("The order payment was rejected by %s", $pending->getEmail()) . "\n");
      }
      elseif ($order->getState() == Data_Icrc_Model_InternalPaymentMethod::STATUS_REJECTED) {
        $_session->addNotice('Order already rejected');
        $pending->delete();
      }
      elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
        $_session->addNotice('Order already validated');
        $pending->delete();
      }
      elseif ($order->getId() < 1) {
        $_session->addNotice('No such order');
        $pending->delete();
      }
      else {
        $_session->addNotice('Order already processed');
        $pending->delete();
      }
    } catch (Mage_Core_Exception $e) {
      Mage::logException($e);
      $_session->addError($e->getMessage());
    } catch (Exception $e) {
      Mage::logException($e);
      if (isset($order))
        $this->_sendPaymentFailedEmail($order, $this->__("An error occures while processing the payment rejection: %s", $e->getMessage()) . "\n");
      $_session->addError($helper->__('An error occured while processing the payment rejection, please contact the store owner for assistance.'));
      $_session->addError($e->getMessage());
    }
  }

  protected function _processPayment($pending) {
    /** @var $_session Mage_Core_Model_Session */
    $_session = Mage::getSingleton('core/session');
    /** @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('sales/order');
    $order->load($pending->getOrderId());

    try {
      if ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
        /** @var $payment Mage_Sales_Model_Order_Payment */
        $payment = $order->getPayment();
        $payment->setStatus(Data_Icrc_Model_InternalPaymentMethod::STATUS_APPROVED);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $this->__('Authorized by manager %s', $pending->getEmail()));
        if (!$order->canInvoice()) {
					Mage::throwException($this->__('Can not create an invoice.'));
				}
        /** @var $invoice Mage_Sales_Model_Order_Invoice */
				$invoice = $order->prepareInvoice();
				$invoice->register();
				$order->addRelatedObject($invoice);
				$order->sendNewOrderEmail()
				      ->setEmailSent(true)
				      ->save();
        $_session->addSuccess('Order validated');
        $pending->delete();
      }
      elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
        $_session->addNotice('Order already validated');
        $pending->delete();
      }
      return true;
    } catch (Mage_Core_Exception $e) {
      Mage::logException($e);
      if (isset($order))
        $this->_sendPaymentFailedEmail($order, $e->getMessage());
      $_session->addError($e->getMessage());
      return false;
    } catch (Exception $e) {
      Mage::logException($e);
      if (isset($order))
        $this->_sendPaymentFailedEmail($order, $this->__("An error occures while processing the payment: %s", $e->getMessage()));
      $_session->addError($this->__('An error occured while processing the payment, please contact the store owner for assistance.'));
      return false;
    }
  }

  protected function _sendPaymentFailedEmail($order, $msg) {
    $_session = Mage::getSingleton('core/session');
    if (!$order) {
      $_session->addNotice('Cannot notify user: no order');
      return;
    }
    if ($order->getId() < 1) {
      $_session->addNotice('Cannot notify user: no order');
      return;
    }
    if (!$order->getQuoteId()) {
      $_session->addNotice('Cannot notify user: no order quote');
      return;
    }
    $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
    if ($quote->getId() < 1) {
      $_session->addNotice('Cannot notify user: no order quote');
      return;
    }
    Mage::helper('checkout')->sendPaymentFailedEmail($quote, $msg);
  }
  
  protected function _lock($pending) {
    $resource = Mage::getSingleton('core/resource');
		$id = $pending->getId();
		$table = $resource->getTableName('data_icrc/payment_pending');
		$sqlCon = $resource->getConnection('core_write');
		$sqlCon->beginTransaction();
		$sqlCon->query("update {$table} set token = token where authorize_id = $id");
		return $sqlCon;
  }
  
  protected function _unlock($sqlCon) {
    $sqlCon->commit();
  }
}

