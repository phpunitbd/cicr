<?php

class Data_Icrc_Block_Form_Element_Autocomplete extends Varien_Data_Form_Element_Text
{
  public function __construct($config) {
    parent::__construct($config);
  }
  
  public function getAfterElementHtml() {
    $for  = $this->getHtmlId();
    $name = str_replace('-', '_', "{$for}_autocompleter");
    $url  = Mage::getUrl('icrc/radar/shipping');
    $type = $this->getForm()->getHtmlIdPrefix() . 'icrc_type' . $this->getForm()->getHtmlIdSuffix();
    
    $html  = "<div id=\"{$for}-choices\" class=\"autocomplete\"></div>";
    $html .= '<script type="text/javascript">//<![CDATA['."\n";
    $html .= "function {$name}_callback(inp, def) { prm = new Hash({'icrc_unit': inp.value}); if ($('{$type}')) prm.set('type', $('{$type}').value); return prm.toQueryString(); }\n";
    $html .= "var {$name} = new Ajax.Autocompleter('{$for}', '{$for}-choices', '{$url}', {callback: {$name}_callback});\n";
    $html .= "$('${for}').observe('click', function() { addr_doForceRefreshGivenHelper({$name}, this); });\n";
    $html .= "\n".'//]]></script>';
    return $html;
  }
}
