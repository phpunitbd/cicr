<?php


class Data_Icrc_Adminhtml_Icrc_DatastudioController extends Mage_Adminhtml_Controller_Action
{
  protected function _isAllowed()
  {
    return Mage::getSingleton('admin/session')->isAllowed('data_icrc');
  }

  public function indexAction()
  {
    $loginOk = false;
    try {
      $return = Mage::helper('data_icrc/datastudio')->login();
      $loginOk = true;
    } catch (Exception $e) {
      Mage::logException($e);
      Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    }
    //Mage::getSingleton('adminhtml/session')->addNotice('f: '.__FILE__);
    //Mage::getSingleton('adminhtml/session')->addNotice(Zend_Debug::dump(Mage::getBaseDir('code'), null, false));
    $this->loadLayout();
    $this->_setActiveMenu('data_icrc');
    if ($loginOk) {
      $this->_addContent($this->getLayout()->createBlock('icrc/adminhtml_datastudio'));
    }
    $this->renderLayout();
  }

  public function getParamJsonAction()
  {
    $project = $this->getRequest()->getParam('project');
    try {
      $return = Mage::helper('data_icrc/datastudio')->getProjectParameters($project);
      $return->status = 'OK';
      foreach ($return->params as &$p) {
        if ($p->bound) $p->values = Mage::helper('data_icrc/datastudio')->getParamValues($p->name->code);
      }
      $this->getResponse()->setHeader('Content-Type', 'application/json', true)
                          ->setBody(Mage::helper('core')->jsonEncode($return));
    } catch (Exception $e) {
      Mage::logException($e);
      $this->_error($e->getMessage());
    }
  }

  public function getParamDomainJsonAction()
  {
    $param = $this->getRequest()->getParam('param');
    try {
      $return = Mage::helper('data_icrc/datastudio')->getParamValues($param);
      $return->status = 'OK';
      $this->getResponse()->setHeader('Content-Type', 'application/json', true)
                          ->setBody(Mage::helper('core')->jsonEncode($return));
    } catch (Exception $e) {
      Mage::logException($e);
      $this->_error($e->getMessage());
    }
  }

  /**
   * This action runs a project and get the returned file "filename", if any, put it
   * in the /var/datastudio folder, and send url to user
   */
  public function runAction()
  {
    $post = $this->getRequest()->getPost();
    try {
      if (empty($post)) {
          Mage::throwException($this->__('Invalid form data.'));
      }
      if (!array_key_exists('run', $post) || !array_key_exists('projname', $post['run']) || empty($post['run']['projname'])) {
          Mage::throwException($this->__('Invalid form data: project name not found.'));
      }
      $project = $post['run']['projname'];
      $args = array();
      if (array_key_exists('param', $post)) {
        foreach ($post['param'] as $param) {
          if (!array_key_exists('name', $param)) {
            Mage::throwException($this->__('Invalid form data: param name not found.'));
          }
          if (!array_key_exists('value', $param)) {
            Mage::throwException($this->__('Invalid form data: param value not found.'));
          }
          if (empty($param['name']))
            continue;
          $args[$param['name']] = $param['value'];
        }
      }
      
      $returned_values = array();
      $return = Mage::helper('data_icrc/datastudio')->run($project, $args);
      if (!is_object($return))
        Mage::throwException('Return is not an object');
      if (is_object($return) && !property_exists($return, 'items'))
        $return->items = array();
      if (is_object($return) && is_object($return->items))
        $return->items = array($return->items);
      if (is_object($return) && is_array($return->items))
        foreach ($return->items as $item) {
          if ($item->name == 'filename' && property_exists($item, 'file')) {
//Mage::getSingleton('adminhtml/session')->addSuccess(Zend_Debug::dump($item, null, false));
            $filename = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'var/datastudio/' . $item->file->filename;
            $file = Mage::getBaseDir('var') . DS . 'datastudio' . DS . $item->file->filename;
            $fp = fopen($file, 'w');
            fwrite($fp, base64_decode($item->file->base64content));
            fclose($fp);
          }
          elseif (property_exists($item, 'string'))
            $returned_values[] = array('name' => $item->name, 'value' => $item->string);
          elseif (property_exists($item, 'integer'))
            $returned_values[] = array('name' => $item->name, 'value' => $item->integer);
        }   

      $message = $this->__('Your project ran successfully.');
      if (isset($filename))
        $message .= " <a href=\"$filename\">".$this->__('Get file').'</a>';
      if (count($returned_values)) {
        $message .= '<br/>' . $this->__('Returned values are:') . ' <div class="datastudio-results">';
        foreach ($returned_values as $val) {
          $message .= "<strong class=\"name\">$val[name]: </strong>$val[value]<br/>";
        }
        $message .= '</div>';
      }
      Mage::getSingleton('adminhtml/session')->addSuccess($message);
    } catch (Exception $e) {
      Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      if (Mage::registry('trace_saved')) {
        $tracemsg = '<small><a href="' . $this->getUrl('*/*/stacktrace') . '">' . $this->__('see trace') . '</a></small>';
        Mage::getSingleton('adminhtml/session')->addError($tracemsg);
      }
    }
    $this->_redirect('*/*');
    //$this->indexAction();
  }

  public function stacktraceAction() {
    $messages = Mage::getSingleton('adminhtml/session')->getLastTrace();
    if (is_object($messages) && property_exists($messages, 'mStrings')) {
      $messages = $messages->mStrings;
    }
    if (!is_array($messages))
      Mage::getSingleton('adminhtml/session')->addError("No messages recorded");
    $this->loadLayout();
    $this->_setActiveMenu('data_icrc');
    if (is_array($messages)) {
      $msg = $this->getLayout()->getBlock('trace');
      $msg->setMessages($messages);
    }
    $this->renderLayout();
  }

  protected function _error($msg) {
      $response = array();
      $response['status'] = 'ERROR';
      $response['error'] = $msg;
      $this->getResponse()->setHeader('Content-Type', 'application/json', true)
                          ->setBody(Mage::helper('core')->jsonEncode($response));
  }
}

