<?php

class Data_Icrc_RadarController extends Mage_Core_Controller_Front_Action
{
  const NOTFOUND = 'noRoute';

  public function shippingAction()
  {
    $collection = null;
    $s = $this->getRequest()->getParam('shipping');
    if ($s == null && $this->getRequest()->getParam('icrc_unit') !== null)
      $s = array('icrc_unit' => $this->getRequest()->getParam('icrc_unit'));
    if ($s) {
      $hint = null;
      if ($s && array_key_exists('icrc_unit', $s))
        $hint = $s['icrc_unit'] . '%';
      switch ($this->getRequest()->getParam('type')) {
        case 'unit':
          $collection = Mage::getModel('data_icrc/radar_acronym')->getCollection();
          if ($hint)
            $collection->addFieldToFilter('code', array('like' => $hint));
          break;
        case 'delegation':
          $collection = Mage::getModel('data_icrc/radar_delegation')->getCollection();
          if ($hint)
            $collection->addFieldToFilter('main_site_name', array('like' => $hint));
          break;
      }
    }
    $collection->getSelect()->limit(15);
    $list = $this->_putInHtmlList($collection);
    $this->getResponse()->setBody($list);
  }

  public function billingAction()
  {
    $collection = null;
    $p = $this->getRequest()->getParam('payment');
    if ($p) {
      if (array_key_exists('icrc_unit', $p)) {
        $_collection = Mage::getModel('data_icrc/radar_acronym')->getCollection();
        $_collection->getSelect()->limit(15);
        $hint = $p['icrc_unit'] . '%';
        if ($hint)
          $_collection->addFieldToFilter('code', array('like' => $hint));
        $collection = new Varien_Data_Collection();
        $i = 0;
        foreach ($_collection as $item) {
          $item->setId($i++);
          $collection->addItem($item);
        }
        $_collection = Mage::getModel('data_icrc/radar_delegation')->getCollection();
        $_collection->getSelect()->limit(15);
        $hint = $p['icrc_unit'] . '%';
        if ($hint)
          $_collection->addFieldToFilter('main_site_name', array('like' => $hint));
        foreach ($_collection as $item) {
          $item->setId($i++);
          $collection->addItem($item);
        }
      }
      elseif (array_key_exists('icrc_cost_center', $p)) {
        $collection = Mage::getModel('data_icrc/radar_costcenter')->getCollection();
        $collection->getSelect()->limit(15);
        $hint = $p['icrc_cost_center'] . '%';
        if ($hint)
          $collection->addFieldToFilter('code', array('like' => $hint));
      }
      elseif (array_key_exists('icrc_objective_code', $p)) {
        $collection = Mage::getModel('data_icrc/radar_objective')->getCollection();
        $collection->getSelect()->limit(15);
        $hint = $p['icrc_objective_code'] . '%';
        if ($hint)
          $collection->addFieldToFilter('g_ocode', array('like' => $hint));
      }
    }
    $list = $this->_putInHtmlList($collection);
    $this->getResponse()->setBody($list);
  }

  public function internalAction()
  {
    $collection = null;
    
    $hint = null;
    $unit = $this->getRequest()->getParam('unit', false);
    $cc = $this->getRequest()->getParam('cost_center', false);
    $oc = $this->getRequest()->getParam('objective_code', false);
    if ($unit !== false) {
      $_collection = Mage::getModel('data_icrc/radar_acronym')->getCollection();
      $_collection->getSelect()->limit(15);
      $hint = $unit . '%';
      if ($hint)
        $_collection->addFieldToFilter('code', array('like' => $hint));
      $collection = new Varien_Data_Collection();
      $i = 0;
      foreach ($_collection as $item) {
        $item->setId($i++);
        $collection->addItem($item);
      }
      $_collection = Mage::getModel('data_icrc/radar_delegation')->getCollection();
      $_collection->getSelect()->limit(15);
      $hint = $unit . '%';
      if ($hint)
        $_collection->addFieldToFilter('main_site_name', array('like' => $hint));
      foreach ($_collection as $item) {
        $item->setId($i++);
        $collection->addItem($item);
      }
    }
    elseif ($cc !== false) {
      $collection = Mage::getModel('data_icrc/radar_costcenter')->getCollection();
      $collection->getSelect()->limit(15);
      $hint = $cc . '%';
      if ($hint)
        $collection->addFieldToFilter('code', array('like' => $hint));
    }
    elseif ($oc !== false) {
      $collection = Mage::getModel('data_icrc/radar_objective')->getCollection();
      $collection->getSelect()->limit(15);
      $hint = $oc . '%';
      if ($hint)
        $collection->addFieldToFilter('g_ocode', array('like' => $hint));
    }
    $list = $this->_putInHtmlList($collection);
    $this->getResponse()->setBody($list);
  }
  
  public function adminhtmlAddressAction() {
    $collection = null;
    $list = $this->_putInHtmlList($collection);
    $this->getResponse()->setBody($list);
  }
  
  protected function _putInHtmlList($collection) {
    $html = '<ul>';
    $count = 0;
    if ($collection) foreach ($collection as $item) {
      $html .= '<li>';
      $html .= $item->getList();
      $html .= '</li>';
      if (++$count == 15)
        break;
    }
    $html .= '</ul>';
    return $html;
  }
  
  public function validatorAction() {
    $this->loadLayout();
    $this->renderLayout();
    $this->getResponse()->setHeader('Content-Type', 'application/x-javascript');
  }

}

