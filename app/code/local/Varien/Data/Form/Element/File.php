<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Varien_Data_Form_Element_File extends Varien_Data_Form_Element_Abstract
{
 
    /**
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }
 
    /**
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
 
        if ($this->getValue()) {
            $url = $this->_getUrl();
 
            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                $url = Mage::getBaseUrl('media'). 'catalog' . DS . 'product' . DS . 'pdf' . DS . $url;
            }
 
            $html = '<a href="'.$url.'" target="_blank">';
            $html.= 'View file';
            $html.='</a> ';
        }
        $this->setClass('input-file');
        $html.= parent::getElementHtml();
        $html.= $this->_getDeleteCheckbox();
 
        return $html;
    }
 
    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('core')->__('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> '.$label.'</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
 
        return $html;
    }
 
    /**
     * Enter description here...
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }
 
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }
 
    /**
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
 
}