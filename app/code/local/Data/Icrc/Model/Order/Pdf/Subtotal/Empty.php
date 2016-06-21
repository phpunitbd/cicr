<?php

// An empty line
class Data_Icrc_Model_Order_Pdf_Subtotal_Empty extends Mage_Sales_Model_Order_Pdf_Total_Default {
  public function getTotalsForDisplay()
  {
    $total = array(
      'amount'    => '',
      'label'     => '',
      'font_size' => 15
    );
    return array($total);
  }
}
