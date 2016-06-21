<?php

class Data_Icrc_Model_Condition_ClientType extends Mage_Rule_Model_Condition_Abstract {

	/**
	 * Validate Rule Condition
	 *
	 * @param Varien_Object $object
	 *
	 * @return bool
	 */
	public function validate(Varien_Object $object) {
    $CT = Mage::helper('data_icrc/attributes')->getCurrentCustomerTypeValue($object->getQuoteId());
    return $this->validateAttribute($CT);
	}

	public function getAttributeElement() {
		$element = parent::getAttributeElement();
		$element->setShowAsText(true);
		return $element;
	}

	public function getInputType() {
		return 'select';
	}

	public function getValueElementType() {
		return 'select';
	}


	public function loadAttributeOptions() {
		$attributes = array(
			'clientType' => Mage::helper('data_icrc')->__('Client Type')
		);

		$this->setAttributeOption($attributes);

		return $this;
	}

  public function getValueSelectOptions()
  {
    return Mage::helper('data_icrc/attributes')->getCustomerTypeSelectOptions();
  }


}


