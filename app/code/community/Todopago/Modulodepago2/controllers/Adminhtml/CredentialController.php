<?php

class Todopago_Modulodepago2_Adminhtml_CredentialController extends Mage_Adminhtml_Controller_Action
{

	protected $_error = array();
	
	protected function callService($modo) 
	{
		require_once(Mage::getBaseDir('lib') . '/metododepago2/vendor/autoload.php');
		$user = new TodoPago\Data\User($this->getRequest()->getPost('user'), $this->getRequest()->getPost('pass'));
		$todopago_connector = Mage::helper('modulodepago2/connector')->getConnector($modo);
		try {
			$user = $todopago_connector->getCredentials($user);
		} catch (Exception $e) {
			$this->_error[] = $e->getMessage();
		}
		return $user;
	}
	
    public function productionAction()
    {
		if($this->getRequest()->getPost('user') != null) {
			$user = $this->callService("prod");
			if(count($this->_error) == 0) {
				$sec = explode(" ",$user->getApikey());
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_idstore").value="'.$user->getMerchant().'";</script>';
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_codigo_seguridad").value="'.$sec[1].'";</script>';
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_apikey").value="'.$user->getApikey().'";</script>';
				echo '<script>window.close();</script>';
				die;				
			}
		}
        //Get current layout state
        $this->loadLayout();          
 
		$this->getResponse()->setBody(
			$this->renderBlock()
		);
    }

    public function developersAction()
    {
		if($this->getRequest()->getPost('user') != null) {
			$user = $this->callService("test");
			$sec = explode(" ",$user->getApikey());
			if(count($this->_error) == 0) {
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_idstore_test").value="'.$user->getMerchant().'";</script>';
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_codigo_seguridad_test").value="'.$sec[1].'";</script>';
				echo '<script>window.opener.document.getElementById("payment_modulodepago2_apikey_test").value="'.$user->getApikey().'";</script>';
				echo '<script>window.close();</script>';
				die;
			}
		}
        //Get current layout state
        $this->loadLayout();          
 
		$this->getResponse()->setBody(
			$this->renderBlock()
		);
    }
	
	protected function renderBlock()
	{
		  return $this->getLayout()->getBlock('head')->toHtml() .
		  $this->getLayout()->createBlock(
					'Mage_Core_Block_Template',
					'credential',
					array('template' => 'modulodepago2/credential.phtml')
				)
			->setData('errors',$this->_error)
			->toHtml();   
	}
}