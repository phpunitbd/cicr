<?php
class Data_Icrc_Model_File_Api_V2 extends Mage_Catalog_Model_Api_Resource
{

public function _construct(){
parent::_construct();
//$this->_init ( 'data_icrc/file_api' );
}

  public function upload_file($name, $content, $mime)
  {
	  $name = basename($name);
	  if (!isset($name) || !isset($mime) || !isset($content)) {
		  $this->_fault('data_invalid', Mage::helper('data_icrc')->__('The upload file is not fully specified.'));
	  }
	  $fileContent = base64_decode($content, true);
	  if (!$fileContent) {
		  $this->_fault('data_invalid', Mage::helper('data_icrc')->__('The file content is not valid base64 data.'));
	  }
    $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();
	  $ioAdapter = new Varien_Io_File();

	  //try {
    // Create temporary directory for api
	  $ioAdapter->checkAndCreateFolder($tmpDirectory);
	  $ioAdapter->open(array('path' => $tmpDirectory));
	  // Write file
	  $ioAdapter->write($name, $fileContent, 0666);
	  //unset($fileContent);

	  //verify file integrity
	  if ($mime == "application/pdf")
	  {
		  $finfo = new finfo(FILEINFO_MIME);
		  $readMimeType = $finfo->file($tmpDirectory . DS . $name);
		  if (strstr($readMimeType, "application/pdf"))
		  {
			  //$ioAdapter->mv($tmpDirectory . DS . $name, $this->_getConfig()->getTmpMediaPath($fileName));
			  //$storageHelper = Mage::helper('core/file_storage_database');
			  $baseDir = Mage::getBaseDir('media'). DS . 'catalog' . DS . 'product' . DS . 'pdf';
			  $fileName = $baseDir . DS . $name;
			  //$config=Mage::getSingleton('catalog/product_media_config');
        if (!is_dir($baseDir)) {
				  if (!@mkdir($baseDir, 0777, true)) {
					  Mage::throwException(Mage::helper('core')->__('Unable to create directory: %s', $path));
				  }
			  }
			  $ioAdapter->mv($tmpDirectory . DS . $name, $fileName);

			  //$storageHelper = Mage::helper('core/file_storage_database');
			  //$storageHelper->saveFile($config->getTmpMediaShortUrl($fileName));
			  //$fileStorageModel = Mage::getModel('core/file_storage_file');
			  //$fileStorageModel->saveFile(array('filename'=>$name,'content'=>$fileContent,'directory'=>"pdf"));
			  //$fileStorageModel->saveFile("pdf" . DS . $name,$fileContent);
			  //$ioAdapter->rmdir($tmpDirectory, true);
			  $ioAdapter->rmdir($tmpDirectory, true);
		  }
		  else
		  {
			  $ioAdapter->rmdir($tmpDirectory, true);
			  $this->_fault('data_invalid', Mage::helper('data_icrc')->__('Only pdf file can be uploaded by this api.'));
		  }
	  }
	  else
	  {
		  $ioAdapter->rmdir($tmpDirectory, true);
		  $this->_fault('data_invalid', Mage::helper('data_icrc')->__('The file content is not valid pdf data.'));
	  }
	  Mage::log("file received name = $name");
	  $result = "true";
	  return $result;
  }

  public function getInvoice($invoiceId) {
    Mage::register('isApiGetInvoice', true);
    try {
      $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
      $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf(array($invoice));
    } catch (Exception $e) {
      Data_Icrc_Helper_Debug::dump($e);
      throw $e;
    }
    $out = $pdf->render();
    return base64_encode($out);
  }

}
