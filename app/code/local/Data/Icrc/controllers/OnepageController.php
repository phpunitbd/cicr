<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';
class Data_Icrc_OnepageController extends Mage_Checkout_OnepageController
{
    protected $isInternal = null;

    public function isInternal() {
      if ($this->isInternal === null) 
        $this->isInternal = (Mage::app()->getStore()->getCode() == 'internal');
      return $this->isInternal;
    }

		private function __debug($msg, $obj = null) {
		  if ($obj !== null) Data_Icrc_Helper_Debug::msgdump($msg, $obj);
		  else Data_Icrc_Helper_Debug::msg($msg);
		}

    public function refreshReviewAction() {
      $result = array();
      $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
      $this->getOnepage()->getQuote()->collectTotals()->save();
      $this->loadLayout('checkout_onepage_review');
      $result['goto_section'] = 'review';
      $result['update_section'] = array(
          'name' => 'review',
          'html' => $this->_getReviewHtml()
      );
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function saveAllstepsAction() {
				$this->__debug("saveAllstepsAction");
        if ($this->_expireAjax()) {
						$this->__debug("expireAjax");
            return;
        }
				$this->__debug("start ...");
				$result = array();
				try {
					$b = $this->_saveBillingAction();
					$this->__debug("saveBillingAction: ", $b);
					if ($b === null)
							return;
					$result = array_merge($result, $b);
					$this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
          if (!$this->isInternal())
            $this->_saveCustomerType();
					$s = $this->_saveShippingAction();
					$this->__debug("saveShippingAction: ", $s);
					if ($s === null)
							return;
					$result = array_merge($result, $s);
					$m = $this->_saveShippingMethodAction();
					$this->__debug("saveShippingMethodAction: ", $m);
					if ($m === null)
							return;
					$result = array_merge($result, $m);
					$p = $this->_savePaymentAction();
					$this->__debug("savePaymentAction: ", $p);
					if ($p === null)
							return;
					$result = array_merge($result, $p);
          if ($this->isInternal())
            $this->_saveIcrcPaymentInfo();
          $result['update_steps'] = $this->_getStepHtml(3);
        } catch (Mage_Payment_Exception $e) {
						$this->__debug("Mage_Payment_Exception: " . $e->getMessage());
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
						$this->__debug("Mage_Core_Exception: " . $e->getMessage());
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
						$this->__debug("Exception: " . $e->getMessage());
            Mage::logException($e);
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        }
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}

    public function refreshShippingAction()
    {
        if ($this->_expireAjax()) {
						$this->__debug("expireAjax");
            return;
        }
				$result = array();
				try {
				  $this->__debug("call saveShippingAction");
				  $result = $this->_saveShippingAction();
					if ($result === null)
							return;
          $method = $this->getRequest()->getPost('shipping_method', '');
          if ($method && $method != '')
            $this->_saveShippingMethodAction();
          $result['html'] = $this->_getShippingMethodsHtml();
          //$result['payment_html'] = $this->_getPaymentMethodsHtml();
        } catch (Mage_Payment_Exception $e) {
            Data_Icrc_Helper_Debug::dump($e);
						$this->__debug("Mage_Payment_Exception: " . $e->getMessage());
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            Data_Icrc_Helper_Debug::dump($e);
						$this->__debug("Mage_Core_Exception: " . $e->getMessage());
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Data_Icrc_Helper_Debug::dump($e);
						$this->__debug("Exception: " . $e->getMessage());
            Mage::logException($e);
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function saveAllstepsInternalAction() {
				$this->__debug("saveAllstepsInternal");
        if ($this->_expireAjax()) {
						$this->__debug("expireAjax");
            return;
        }
        if (!$this->isInternal()) {
          $err = array('error' => 'Not on internal site');
          $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($err));
          return;
        }
				$this->__debug("start ...");
				$result = array();
				try {
					$this->_saveValidationInfo();
          $this->_saveIcrcPaymentInfo();
					$this->_saveInternalShippingInfo();
					$p = $this->_savePaymentAction();
					$this->__debug("savePaymentAction: ", $p);
					if ($p === null)
							return;
					$result = array_merge($result, $p);
          $result['update_steps'] = $this->_getStepHtml(3);
        } catch (Mage_Payment_Exception $e) {
						$this->__debug("Mage_Payment_Exception: " . $e->getMessage());
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
						$this->__debug("Mage_Core_Exception: " . $e->getMessage());
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
						$this->__debug("Exception: " . $e->getMessage());
            Mage::logException($e);
						$result['goto_section'] = 'allsteps';
            $result['error'] = $e->getMessage();
        }
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function saveOrderAction() {
      $data = $this->getRequest()->getPost('payment', false);
      if (!$data) // We did not re-submit payment (as it has been changed), so we must collect totals by hand
        $this->getOnepage()->getQuote()->getPayment()->getQuote()->collectTotals();
      if ($this->isInternal()) {
        $icrcInternalInfo = Mage::getSingleton('checkout/session')->getIcrcInternalInfo();
        if ($icrcInternalInfo) {
          $setUnit = 1;
          Data_Icrc_Helper_Debug::dump($icrcInternalInfo);
          // If we're willing to save address in addressbook, add unit/type info as it's not recorded in quote
          $address = $this->getOnepage()->getQuote()->getShippingAddress();
          $unit = $address->getIcrcUnit();
          Data_Icrc_Helper_Debug::msgdump('previous unit: ', $unit);
          if (empty($unit)) {
            $address->setIcrcUnit($icrcInternalInfo->getUnit());
            $address->setIcrcType($icrcInternalInfo->getType());
            Data_Icrc_Helper_Debug::dump($address->getData());
          }
        }
      }
      parent::saveOrderAction();
      if (isset($setUnit) && $setUnit) {
        $address = $this->getOnepage()->getQuote()->getShippingAddress();
        Data_Icrc_Helper_Debug::dump($address->getData());
      }
    }

    public function _saveBillingAction()
    {
        if (!$this->getRequest()->isPost()) {
						$this->__debug("ajaxRedirectResponse");
            $this->_ajaxRedirectResponse();
            return null;
        }

        $data = $this->getRequest()->getPost('billing', array());
        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }
        $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

        if (isset($result['error'])) {
            $this->__debug(Zend_debug::dump($result, null, false));
            if (isset($result['message'])) {
                if (is_string($result['message']))
						        throw new Exception($result['message']);
                elseif (is_array($result['message']))
						        throw new Exception(implode("\n", $result['message']));
            }
						throw new Exception('Error# ' . $result['error']);
        }
				return $result;
    }

    public function _saveShippingAction()
    {
        if (!$this->getRequest()->isPost()) {
						$this->__debug("ajaxRedirectResponse");
            $this->_ajaxRedirectResponse();
            return null;
        }

        $data = $this->getRequest()->getPost('shipping', array());
        $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
        $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

        if (isset($result['error'])) {
            if (isset($result['message'])) {
                if (is_string($result['message']))
						        throw new Exception($result['message']);
                elseif (is_array($result['message']))
						        throw new Exception(implode("\n", $result['message']));
            }
						throw new Exception('Error# ' . $result['error']);
        }
				return $result;
    }

    public function _saveShippingMethodAction()
    {
        if (!$this->getRequest()->isPost()) {
						$this->__debug("ajaxRedirectResponse");
            $this->_ajaxRedirectResponse();
            return null;
        }

        $data = $this->getRequest()->getPost('shipping_method', '');
        if ($this->isInternal()) {
          if ($data == Data_Icrc_Model_Carrier_IcrcInternal::AUTO) {
            // Replaces AUTO by the first computed carrier
            $res = $this->getOnepage()->getQuote()->getShippingAddress()->collectShippingRates()->getShippingRatesCollection();
            foreach ($res as $r) {
              $data = $r->getCode();
              break;
            }
          }
        }
        $result = $this->getOnepage()->saveShippingMethod($data);
        /*
        $result will have erro data if shipping method is empty
        */
        if(!$result) {
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                    array('request'=>$this->getRequest(),
                        'quote'=>$this->getOnepage()->getQuote()));
            // have to setTotalsCollectedFlag to false to re-compute totals with the new shipping method
            $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        }
        $this->getOnepage()->getQuote()->collectTotals()->save();
				return $result;
    }

		public function _savePaymentAction()
    {
        if (!$this->getRequest()->isPost()) {
						$this->__debug("ajaxRedirectResponse");
            $this->_ajaxRedirectResponse();
            return null;
        }

        // set payment to quote
        $result = array();
        $data = $this->getRequest()->getPost('payment', array());

        // Here we check if grand total is free, and if so, switch to free
        $quote = $this->getOnepage()->getQuote();
        if (!$this->isInternal() && ($quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount()) == 0) {
          $data['method'] = 'free';
          $set_payment_free = true;
        }

        $result = $this->getOnepage()->savePayment($data);
        if (isset($set_payment_free) && $set_payment_free)
          $result['payment_changed'] = 'free';

        // get section and redirect data
        $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
        if (empty($result['error']) && !$redirectUrl) {
            $this->loadLayout('checkout_onepage_review');
            $result['goto_section'] = 'review';
            $result['update_section'] = array(
                'name' => 'review',
                'html' => $this->_getReviewHtml()
            );
        }
        if ($redirectUrl) {
            $result['redirect'] = $redirectUrl;
        }
				return $result;
    }

    protected function _saveCustomerType() {
      $data = $this->getRequest()->getPost('billing:customer_type', '');
      if (empty($data))
        throw new Mage_Core_Exception('Cannot find customer type data');
      $helper = Mage::helper('data_icrc/attributes');
      $helper->setCustomerTypeValue($this->getOnepage()->getQuote(), $data);
    }

    public function _saveInternalShippingInfo()
    {
      // Shipping data
      $data = $this->getRequest()->getPost('shipping', array());
      $helper = Mage::helper('data_icrc/attributes');
      $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
      if ($data['icrc_type'] == 'unit') {
        $address_data = Mage::helper('data_icrc/internal')->getUnitAdressData($data['icrc_unit']);
        $address_data['firstname'] = $data['firstname'];
        $address_data['lastname'] = $data['lastname'];
        if (!empty($data['prefix']))
          $address_data['prefix'] = $data['prefix'];
        if (array_key_exists('save_in_address_book', $data)) {
          $address_data['save_in_address_book'] = $data['save_in_address_book'];
          $address_data['icrc_type'] = $data['icrc_type'];
          $address_data['icrc_unit'] = $data['icrc_unit'];
        }
      }
      else
        $address_data = $data;
      if ($customerAddressId !== false && $customerAddressId !== '') {
        $icrc_com = $data['icrc_com'];
        $data = Mage::getModel('customer/address')->load($customerAddressId)->getData();
        $data['icrc_com'] = $icrc_com;
      }
      $helper->setIcrcShippingInfo($this->getOnepage()->getQuote(), $data);
      $result = $this->getOnepage()->saveShipping($address_data, $customerAddressId);
      if (isset($result['error'])) {
        if (isset($result['message'])) {
          if (is_string($result['message']))
		        throw new Exception($result['message']);
          elseif (is_array($result['message']))
		        throw new Exception(implode("\n", $result['message']));
        }
				throw new Exception('Error# ' . $result['error']);
      }
      if (array_key_exists('save_in_address_book', $data)) {
        $icrcInternalInfo = new Varien_Object();
        $icrcInternalInfo->setType($data['icrc_type'])->setUnit($data['icrc_unit']);
        Mage::getSingleton('checkout/session')->setIcrcInternalInfo($icrcInternalInfo);
      }

      // Shipping method: auto
			$this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
      $res = $this->getOnepage()->getQuote()->getShippingAddress()->collectShippingRates()->getShippingRatesCollection();
      $rate = Data_Icrc_Model_Carrier_IcrcInternal::selectAuto($res, $this->getOnepage()->getQuote(), $data);
      $result = $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod($rate->getCode())->save();
      Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
              array('request' => $this->getRequest(),
                  'quote' => $this->getOnepage()->getQuote()));
      // have to setTotalsCollectedFlag to false to re-compute totals with the new shipping method
      $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
      $this->getOnepage()->getQuote()->collectTotals()->save();
    }

    public function _saveValidationInfo() {
      $data = $this->getRequest()->getPost('validation', array());
      $helper = Mage::helper('data_icrc/attributes');
      $helper->setIcrcValidationInfo($this->getOnepage()->getQuote(), $data);
    }

    protected function _saveIcrcPaymentInfo() {
      $data = $this->getRequest()->getPost('payment', array());
      $helper = Mage::helper('data_icrc/attributes')->setIcrcPaymentInfo($this->getOnepage()->getQuote(), $data);
      $address_data = Mage::helper('data_icrc/internal')->getUnitAdressData($data['icrc_unit']);
      $result = $this->getOnepage()->saveBilling($address_data, false);
      if (isset($result['error'])) {
        if (isset($result['message'])) {
          if (is_string($result['message']))
		        throw new Exception($result['message']);
          elseif (is_array($result['message']))
		        throw new Exception(implode("\n", $result['message']));
        }
				throw new Exception('Error# ' . $result['error']);
      }
    }

    protected function _getStepHtml($stepId) {
      $steps = $this->getLayout()->getBlock('checkout_steps');
      if (!$steps)
        $steps = $this->getLayout()->createBlock('Data_Icrc_Block_Checkout_Steps', 'checkout_steps',
                                                 array('template' => 'checkout/steps.phtml'));
      return $steps->setStep($stepId)->toHtml();
    }
    
    public function indexAction() {
      // Check if we can guess an address from the quote data
      $quote = $this->getOnepage()->getQuote();
      $this->getOnepage()->initCheckout();
      $address = $quote->getShippingAddress();
      $country = $address->getCountryId();
      $rates = $address->getGroupedAllShippingRates();
      if (!empty($country) && empty($rates)) {
        // If we have a shipping country and no rates, then save shipping address (that collects rates)
        try {
          $this->getOnepage()->saveShipping($address->getData(), $address->getCustomerAddressId());
        } catch (Exception $e) {}
      }
      parent::indexAction();
    }

    public function failureAction() {
      $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
      $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

      if (!$lastQuoteId || !$lastOrderId) {
        $this->_redirect('checkout/cart');
        return;
      }

      $_session = Mage::getSingleton('checkout/session');

      $this->loadLayout();
      $this->_initLayoutMessages('checkout/session');
      if ($id = $_session->getLastOrderId()) {
        $order = Mage::getModel('sales/order')->load($id);
        if ($order->getId()) {
          $block = $this->getLayout()->getBlock('checkout.failure');
          if ($block) {
            $block->setOrder($order);
            $block->setRealOrderId($order->getIncrementId());
          }
        }
      }
      $this->renderLayout();
    }
}

