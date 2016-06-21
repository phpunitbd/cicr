<?php

class Data_Icrc_Block_Page_Html_Adminhtml_Head extends Mage_Adminhtml_Block_Page_Head
{
  protected $specificJs = null;
  public function addSpecificJs($file) {
    if (empty($file))
      return;
    if (!$this->specificJs)
      $this->specificJs = array();
    $this->specificJs[] = $file;
  }
  
  public function _getSpecificHtml() {
    if ($this->specificJs == null)
      return '';
    $html = '';
    foreach ($this->specificJs as $js) {
      $html .= "<script type=\"text/javascript\" src=\"{$js}\"></script>\n";
    }
    return $html;
  }

  public function getCssJsHtml() {
    return parent::getCssJsHtml() . $this->_getSpecificHtml();
  }
}

