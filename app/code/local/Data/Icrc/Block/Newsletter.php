<?php

class Data_Icrc_Block_Newsletter extends Mage_Core_Block_Template
{
    /**
     * Get Customer Subscription Object Information
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
        }

        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }
}