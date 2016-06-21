<?php

class Data_Icrc_Model_Resource_Productdeleted_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Detail Collection Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('data_icrc/productdeleted');
    }
    
    /**
     * Add attribute filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $condition
     * @param string $operator
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($attribute === null) {
            $this->getSelect();
            return $this;
        }

        if (is_numeric($attribute)) {
            $attribute = $this->getEntity()->getAttribute($attribute)->getAttributeCode();
        } else if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            $attribute = $attribute->getAttributeCode();
        }

        if (is_array($attribute)) {
            $sqlArr = array();
            foreach ($attribute as $condition) {
                $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
            }
            $conditionSql = '('.implode(') OR (', $sqlArr).')';
        } else if (is_string($attribute)) {
            if ($condition === null) {
                $condition = '';
            }
            $conditionSql = $this->_getAttributeConditionSql($attribute, $condition, $joinType);
        }

        if (!empty($conditionSql)) {
            $this->getSelect()->where($conditionSql, null, Varien_Db_Select::TYPE_CONDITION);
        } else {
            Mage::throwException('Invalid attribute identifier for filter ('.get_class($attribute).')');
        }

        return $this;
    }
}

