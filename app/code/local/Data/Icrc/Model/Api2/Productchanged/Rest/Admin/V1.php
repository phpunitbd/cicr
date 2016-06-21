<?php

class Data_Icrc_Model_Api2_Productchanged_Rest_Admin_V1 extends Data_Icrc_Model_Api2_Productchanged
{
    /**
     * 
     * @return type
     */
    protected function _retrieveCollection() {
        $timestamp = $this->getRequest()->getParam('date');
        $date = date('Y-m-d H:i:s', $timestamp);
        $productsCollection = array();
        $this->getSession()->setData('date', $date);
        return $productsCollection;
    }
}
