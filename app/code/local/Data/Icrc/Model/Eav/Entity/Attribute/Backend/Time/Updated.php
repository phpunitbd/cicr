<?php

class Data_Icrc_Model_Eav_Entity_Attribute_Backend_Time_Updated extends Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated
{
    /**
     * Set modified date
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated
     */
    public function beforeSave($object)
    {
        if(!Mage::getSingleton('core/session')->hasUpdatedAt()) {
            $object->setData($this->getAttribute()->getAttributeCode(), Varien_Date::now());
        }
        return $this;
    }
}
