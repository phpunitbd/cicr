<?php

class Data_Icrc_Model_Observer_Captcha extends Mage_Captcha_Model_Observer
{
  const Exception = 0;
  const JSon = 1;
  const Message = 2;

  protected function check($controller, $formId, $errorType = self::Exception, $errorUrl = '*/*/index') {
    $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
    if ($captchaModel->isRequired()) {
      if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
        switch ($errorType) {
          case self::Exception:
            Mage::throwException(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
            break;
          case self::Message:
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            Mage::getSingleton('core/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
            $controller->getResponse()->setRedirect(Mage::getUrl($errorUrl));
            break;
          case self::JSon:
            $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            $result = array('class' => 'error', 'message' => Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
            $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            break;
        }
      }
    }
    return $this;
  }

    public function contactCheck($observer) {
        return $this->check($observer->getControllerAction(), 'contact', self::Message);
    }

    public function newsletterCheck($controller) {
        return $this->check($controller, 'newsletter');
    }
  
    public function otherlanguageCheck($observer)
    {
        return $this->check($observer->getControllerAction(), 'contact', self::JSon);
    }
}

