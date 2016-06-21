<?php

class Data_Icrc_Model_InternalPaymentMethod extends Mage_Payment_Model_Method_Abstract
{
  protected $_code = 'icrc';
  protected $_canAuthorize = true;
  protected $_canUseCheckout = true;
  protected $_formBlockType = 'icrc/payment_icrc_form';
  protected $_order;

  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
  const STATUS_TO_BE_VALIDATED_BY_MANAGER = 'to_be_validated_by_manager';

	/**
	 * Get initialized flag status
	 *
	 * @return true
	 */
	public function isInitializeNeeded()
	{
		return true;
	}

	/**
	 * Instantiate state and set it to state onject
	 * 
	 * @param string
	 * @param Varien_Object
	 * @return Varien_Object
	 */
	public function initialize($paymentAction, $stateObject)
	{
    // Mage::app()->getStore()->isAdmin()

		$stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
		$stateObject->setStatus(self::STATUS_TO_BE_VALIDATED_BY_MANAGER);
		$stateObject->setIsNotified(false);

    $order = $this->getOrder();
    $order->setCanSendNewEmailFlag(false);

    $helper = Mage::helper('data_icrc/attributes');
    $attr = $helper->getOrderAttributes($order);
    foreach ($attr as $a) {
      if ($a->getTitle() == Data_Icrc_Helper_Attributes::VALIDATION_EMAIL) {
        $email = $a->getValue();
        break;
      }
    }
    if (!isset($email))
      Mage::throwException('Cannot find validation e-mail in order attributes');

    $rand = $this->_getRand();
    $pending = Mage::getModel('data_icrc/payment_pending');
    $pending->setToken($rand)->setEmail($email)->setOrderId($order->getId())->save();

    $pending->sendEmail($order->getCustomer());
	}


  public function getOrder()
  {
    if (!$this->_order) {
		  try {
			  $this->_order = $this->getInfoInstance()->getOrder();
			  if (! $this->_order) {
				  $orderId = $this->getSession()->getQuote()->getReservedOrderId();
				  $order = Mage::getModel('sales/order');
				  $order->loadByIncrementId($orderId);
				  if ($order->getId()) {
					  $this->_order = $order;
				  }
			  }
		  } catch(Exception $e){
			  $id = $this->getSession()->getLastOrderId();
			  $this->_order = Mage::getModel('sales/order')->load($id);
		  }
    }
    return $this->_order;
  }

  protected function _getRand()
  {
    $rand = '';
    do {
      $rand .= base_convert(mt_rand(1, mt_getrandmax()), 10, 36);
    } while (strlen($rand) < 50);
    return substr($rand, 0, 50);
  }

  public function canUseForCountry($country)
  {
    return true;
  }
}

