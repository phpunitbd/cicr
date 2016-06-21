<?php

class Data_Icrc_Model_Observer_Rules
{

	/**
	 * Event: salesrule_rule_condition_combine
	 *
	 * @param $observer
	 *
	 * @return
	 */
	public function addConditionToSalesRule($observer) {
		$additional = $observer->getAdditional();
		$conditions = (array) $additional->getConditions();

		$conditions = array_merge_recursive($conditions, array(
			array('label' => Mage::helper('data_icrc')->__('Client Type'), 
            'value' => 'data_icrc/condition_clientType'),
		));

		$additional->setConditions($conditions);
		$observer->setAdditional($additional);

		return $observer;
	}
}

