<?php

class Data_Icrc_Model_Newsletter_Api extends Mage_Catalog_Model_Api_Resource
{
  protected function _getReturnObject($subscriber) {
    $return = new stdClass();
    $return->item = array($this->_getReturnSubscriber($subscriber));
    Data_Icrc_Helper_Debug::dump($return);
    return $return;
  }
  
  protected function _getReturnSubscriber($subscriber) {
    $return = new stdClass();
    foreach ($subscriber->getData() as $key => $value) {
      $return->{$key} = $value;
    }
    return $return;
  }
  
  public function info($filter) {
    Data_Icrc_Helper_Debug::dump($filter);
    if (!empty($filter->subscriber_id)) {
      $subscriber = Mage::getModel('newsletter/subscriber')->load($filter->subscriber_id);
      if (!$subscriber->getId())
        return null;
      return $this->_getReturnObject($subscriber);
    }
    
    $subscribers = Mage::getModel('newsletter/subscriber')->getCollection();
    if(!empty($filter->email)) {
      $subscribers->addFieldToFilter('subscriber_email', array('like' => $filter->email));
    }
    if(!empty($filter->store_id)) {
      $subscribers->addFieldToFilter('store_id', array($filter->store_id));
    }
    if (!$subscribers->count())
      return null;
    $return = new stdClass();
    foreach ($subscribers as $subscriber)
      $return->item[] = $this->_getReturnSubscriber($subscriber);
    Data_Icrc_Helper_Debug::dump($return);
    return $return;
  }
}

