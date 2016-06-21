<?php

class Data_Icrc_Block_Adminhtml_Customer_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses
{
  public function initForm() {
    parent::initForm();
    $customer = Mage::registry('current_customer');
    if ($customer->getWebsiteId() != Mage::helper('data_icrc/internal')->getInternalWebsiteId())
      return $this;
      
    // Internal customer
    $form = $this->getForm();
    $_def = array('class' => 'required-entry', 'required' => '1');
    $type = array('label' => 'Type', 'name' => 'icrc_type', 'html_id' => 'icrc_type_entry', 
                  'values' => array(
                                    array('value'=> 'unit', 'label' => 'Unit'), 
                                    array('value'=> 'delegation', 'label' => 'Delegation')));
    $unit = array('label' => 'Unit or Delegation', 'name' => 'icrc_unit', 'html_id' => 'icrc_unit_entry');
    $subform = $form->getElement('address_fieldset');
    $subform->addField('icrc_type', 'select', array_merge($type, $_def));
    $subform->addType('autocomplete', 'Data_Icrc_Block_Form_Element_Autocomplete');
    $_def['class'] .= ' radar-validate-unit-delegation';
    $subform->addField('icrc_unit', 'autocomplete', array_merge($unit, $_def));
    //Data_Icrc_Helper_Debug::dump($subform);
    return $this;
  }
}
