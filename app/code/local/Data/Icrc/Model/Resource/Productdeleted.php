<?php

class Data_Icrc_Model_Resource_Productdeleted extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Detail Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('data_icrc/productdeleted', 'entity_id');
    }
}
