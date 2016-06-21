<?php

class Data_Icrc_Model_Api2_Renderer_Xml_XMLExtended extends SimpleXMLElement
{
    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $namespace
     * @return type
     */    
    public function addChild($name, $value = null, $namespace = null) {
        if($value != null){
            $node = parent::addChild($name,null, $namespace);
            $domnode = dom_import_simplexml($node);
            $no = $domnode->ownerDocument;
            $domnode->appendChild($no->createCDATASection($value));
            return $node;
        }
        else {
            return parent::addChild($name, $value, $namespace);
        }
    }
}
