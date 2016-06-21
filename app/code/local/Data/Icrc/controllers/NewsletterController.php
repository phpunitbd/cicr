<?php

class Data_Icrc_NewsletterController extends Mage_Core_Controller_Front_Action
{
  public function indexAction()
  {
    $this->loadLayout();
    $this->renderLayout();
  }

  public function registerPostAction()
  {
    $captcha = Mage::getSingleton('data_icrc/observer_captcha');
    try {
      $captcha->newsletterCheck($this);
      $email = $this->getRequest()->getParam('email');
      $unsuscribe = $this->getRequest()->getParam('unsuscribe');
      $back = $this->getRequest()->getParam('back');
      $session = Mage::getSingleton('customer/session');
      $subscriber = Mage::getModel('newsletter/subscriber');
      if ($unsuscribe == 'true') {
        $subscriber->loadByEmail($email);
        if ($subscriber->getId() && 
            $subscriber->getSubscriberStatus() != Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
          $subscriber->unsubscribe();
          Mage::getSingleton('core/session')->addSuccess($this->__('%s unsuscribed from newsletter', $email));
        }
        else
          Mage::throwException($this->__('Address not suscribed to newsletter'));
      }
      else {
        $subscriber->subscribe($email);
        Mage::getSingleton('core/session')->addSuccess($this->__('%s suscribed to newsletter', $email));
      }
    } catch (Exception $e) {
      Mage::getSingleton('core/session')->addError($e->getMessage());
    }
    if ($back)
      Mage::app()->getResponse()->setRedirect($back);
    else
      $this->_redirect('*/*/index');
  }
  
    public function registerajaxPostAction()
    {
        $result = array();
        $captcha = Mage::getSingleton('data_icrc/observer_captcha');
        try {
            $captcha->newsletterCheck($this);
            $email = $this->getRequest()->getParam('email');
            $_logged = $this->getRequest()->getParam('logged');
            $unsuscribe = $this->getRequest()->getParam('unsuscribe');
            $back = $this->getRequest()->getParam('back');
            $session = Mage::getSingleton('customer/session');
            $subscriber = Mage::getModel('newsletter/subscriber');
            if ($unsuscribe == 'true') {
                $subscriber->loadByEmail($email);
                if ($subscriber->getId() && 
                    $subscriber->getSubscriberStatus() != Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
                    $subscriber->unsubscribe();
                    $newsletterBlock = Mage::app()->getLayout()
                        ->createBlock('data_icrc/newsletter')
                        //->setCaptcha(Mage::app()->getLayout()->createBlock('captcha/captcha')->toHtml())
                        ->setTemplate('newsletter/data_icrc_register_mini_form.phtml');
                    $result['newsletter'] = $newsletterBlock->toHtml();
                    $result['success'] = $this->__('%s unsuscribed from newsletter', $email);
                }
                else {
                    $result['error'] = $this->__('Address not suscribed to newsletter');
                }
            }
            else {
              $subscriber->subscribe($email);
              $newsletterBlock = Mage::app()->getLayout()
                        ->createBlock('data_icrc/newsletter')
                        //->setCaptcha(Mage::app()->getLayout()->createBlock('captcha/captcha')->toHtml())
                        ->setTemplate('newsletter/data_icrc_register_mini_form.phtml');
              $result['newsletter'] = $newsletterBlock->toHtml();
              $result['success'] = $this->__('%s suscribed to newsletter', $email);
            }
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }
        $result['logged'] = $_logged;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        return;
    }
}

