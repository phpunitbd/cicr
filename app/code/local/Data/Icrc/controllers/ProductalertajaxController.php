<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Data_Icrc_ProductalertajaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Function response json
     */
    private function _setBody($class, $message) {
        $this->getResponse()->setBody(
            Zend_Json::encode(array('class'=>$class, 'message'=>$message))
        );
    }
    
    /**
     * 
     * @return type
     */
    public function addAction()
    {
        $session = Mage::getSingleton('catalog/session');      
        
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$productId) {
            $this->_setBody('error', $this->__('Not enough parameters.'));
            return ;
        }
        
        $model  = Mage::getModel('productalert/stock')
            ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
            ->setProductId($productId)
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByParam();
        if ($model->getId()) {
            $removeAlertBlock = Mage::app()->getLayout()
                ->createBlock('core/template')
                ->setProductId($productId)
                ->setTemplate('productalert/stockremove.phtml');

            $this->_setBody('success', $removeAlertBlock->toHtml());
            return;
        } else {
            try {
                $model = Mage::getModel('productalert/stock')
                    ->setCustomerId(Mage::getSingleton('customer/session')->getId())
                    ->setProductId($productId)
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
                $model->save();
                $this->_setBody('success', $this->__('Alert subscription has been saved.'));
                return;
            }
            catch (Exception $e) {
                $this->_setBody('error', $this->__('Not enough parameters.'));
                $session->addException($e, $this->__('Unable to update the alert subscription.'));
                return;
            }
        }
    }
    
    /**
     * 
     * @return type
     */
    public function removeAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product_id');

        if (!$productId) {
            $this->_setBody('error', $this->__('Not enough parameters.'));
            return;
        }

        $session = Mage::getSingleton('catalog/session');

        try {
            $model  = Mage::getModel('productalert/stock')
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setProductId($productId)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $this->_setBody('success', $this->__('You will no longer receive stock alerts for this product.'));
            return;
        }
        catch (Exception $e) {
            $this->_setBody('error', $this->__('Unable to update the alert subscription.'));
            return;
        }
    }
}