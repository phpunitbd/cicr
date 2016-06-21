<?php

class Data_Icrc_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
  protected function _doJoinAttr(&$collection, $alias, $id) {
    $talias = 'tbl_' . $alias;
    $collection->getSelect()->joinLeft(
      array($talias => $collection->getTable('data_icrc/order_attribute_value')),
      '`' . $talias . '`.quote_id=`sales/order`.quote_id and `' . $talias . '`.order_attribute_id = ' . $id,
      array($alias => 'value')
    );
  }

  protected function _prepareCollection()
  {
    $helper = $this->helper('data_icrc/attributes');
//var_dump($this->_getCollectionClass());
    $collection = Mage::getResourceModel('data_icrc/order_grid_collection');

    $collection->join('sales/order', '`sales/order`.entity_id=main_table.entity_id', 
                      array('shipping_description' => 'shipping_description', 'quote_id' => 'quote_id'), null, 'left');

    $this->_doJoinAttr($collection, 'shipping_unit', $helper->getShippingUnitId());
    $this->_doJoinAttr($collection, 'shipping_com', $helper->getShippingComId());
    $this->_doJoinAttr($collection, 'billing_unit', $helper->getUnitId());
    $this->_doJoinAttr($collection, 'billing_cc', $helper->getCCId());
    $this->_doJoinAttr($collection, 'billing_oc', $helper->getOCId());
    $this->_doJoinAttr($collection, 'client_type', $helper->getCustomerTypeId());

    $this->setCollection($collection);

    $ret = Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

    return $ret;
  }

  protected function shippingFilterConditionCallback($collection, $column) {
    $cond = $column->getFilter()->getCondition();
    $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
    if ($field && isset($cond)) {
      $this->getcollection()->setIsEavMode(true);
      $this->getCollection()->addFieldToFilter(array($field, 'tbl_shipping_unit.value'), array($cond, $cond));
    }
  }

  protected function commentFilterConditionCallback($collection, $column) {
    $cond = $column->getFilter()->getCondition();
    if (isset($cond)) {
      $this->getcollection()->setIsEavMode(true);
      $this->getCollection()->addFieldToFilter(array('tbl_shipping_com.value'), array($cond));
    }
  }

  protected function billingFilterConditionCallback($collection, $column) {
    $cond = $column->getFilter()->getCondition();
    $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
    if ($field && isset($cond)) {
      $this->getcollection()->setIsEavMode(true);
      $this->getCollection()->addFieldToFilter(
        array($field, 'tbl_billing_unit.value', 'tbl_billing_oc.value', 'tbl_billing_cc.value', 'tbl_client_type.value'),
        array($cond, $cond, $cond, $cond, $cond)
      );
    }
  }

  protected function _prepareMassaction()
  {
    parent::_prepareMassaction();
    $this->getMassactionBlock()->addItem('validate_order', array(
         'label'=> Mage::helper('sales')->__('Validate Order'),
         'url'  => $this->getUrl('*/icrc_order/massValidate'),
    ));
    return $this;
  }

  protected function _prepareColumns() {
    parent::_prepareColumns();

    $shipping = $this->getColumn('shipping_name');
    $shipping['renderer'] = 'Data_Icrc_Block_Adminhtml_Order_ShippingRenderer';
    $shipping['filter_condition_callback'] = array($this, 'shippingFilterConditionCallback');

    $billing = $this->getColumn('billing_name');
    $billing['renderer'] = 'Data_Icrc_Block_Adminhtml_Order_BillingRenderer';
    $billing['filter_condition_callback'] = array($this, 'billingFilterConditionCallback');

    $filter_fix = array('real_order_id', 'store_id', 'created_at', 'grand_total', 'base_grand_total', 'status');
    foreach ($filter_fix as $cn) {
      $c = $this->getColumn($cn);
      $c['filter_index'] = 'main_table.' . $c['index'];
    }

    $table = $this->getTable('sales/order');
    $this->addColumnAfter('shipping_com', array(
        'header' => Mage::helper('checkout')->__('Shipping Comment'),
	'index' => $table.'shipping_com'
    ), 'shipping_name');
    $this->sortColumnsByOrder();

    $comment = $this->getColumn('shipping_com');
    $comment['filter_condition_callback'] = array($this, 'commentFilterConditionCallback');

    $this->addColumnAfter('shipping_method', array(
        'header' => Mage::helper('checkout')->__('Shipping Method'),
        'index' => $table.'shipping_description'
    ), 'shipping_method');
    $this->sortColumnsByOrder();

    $this->helper('data_icrc/attributes')->loadAdminLabels();

    return $this;
  }
}

