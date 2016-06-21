<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Data_Icrc_OtherlanguageController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    
    /**
     * Function response json
     */
    private function _setBody($class, $message) {
        $this->getResponse()->setBody(
            Zend_Json::encode(array('class'=> $class, 'message'=> $message))
        );
    }
    
    /**
     * Display form of demand other language
     * @return type
     */
    public function orderAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product_id');
        $lang  = $this->getRequest()->getParam('lang');
        
        if (!$productId || !$lang) {
            $this->_setBody('error', $this->__('Not enough parameters.'));
            return ;
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        Mage::getSingleton('customer/session')->setData('contact_show_captcha', 1);
        $captcha = Mage::getBlockSingleton('captcha/captcha')
            ->setFormId('contact')
            ->setImageWidth(230)
            ->setImageHeight(50);
        
        $block = Mage::app()->getLayout()
            ->createBlock('core/template')
            ->setFormAction(Mage::getUrl('icrc/otherlanguage/post'))
            ->setProduct($product)
            ->setLang($lang)
            ->setTemplate('catalog/product/view/order_language.phtml')
            ->setChild('captcha', $captcha);
        $this->_setBody('success', $block->toHtml());
    }
    
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                $rcpt_email = Mage::helper('data_icrc')->getMailFromLang($post['lang']);

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $rcpt_email,
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                    return;
                }

                $translate->setTranslateInline(true);

                $this->_setBody('success', Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                $this->_setBody('error', Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                return;
            }

        } else {
            $this->_setBody('error', $this->__('Not enough parameters.'));
            return;
        }
    }
}