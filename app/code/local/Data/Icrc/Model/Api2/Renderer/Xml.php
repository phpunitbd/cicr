<?php

class Data_Icrc_Model_Api2_Renderer_Xml extends Mage_Api2_Model_Renderer_Xml
{
    /**
     * 
     * @param type $data
     * @return type
     */
    public function render($data)
    {
        $writer = Mage::getModel('data_icrc/api2_renderer_xml_writer');
        return $writer->render($data);
    }
}