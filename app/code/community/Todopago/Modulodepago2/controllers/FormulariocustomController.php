<?php
class Todopago_Modulodepago2_FormulariocustomController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        //Get current layout state
        $this->loadLayout();          
 
		$order_nro = $this->getRequest()->getParam('order');
		$order = Mage::getSingleton('sales/order')->loadByIncrementId($order_nro);

		$apyn = $order->getBillingAddress()->getFirstname() . " " . $order->getBillingAddress()->getLastname();

		$this->getResponse()->setBody(
		  $this->getLayout()->getBlock('head')->toHtml() .
		  $this->getLayout()->createBlock(
					'Mage_Core_Block_Template',
					'formulariocustom',
					array('template' => 'modulodepago2/formulariocustom.phtml')
				)->setData('requestkey',$this->getRequest()->getParam('requestKey'))
				 ->setData('merchant',Mage::helper('modulodepago2/ambiente')->get_merchant())
				 ->setData('amount',number_format($order->getGrandTotal(), 2, ".", ""))
				 ->setData('mail',$order->getCustomerEmail())
				 ->setData('nombre',$apyn)
				 ->setData('orden', $order_nro)
			->toHtml()  
		);
    }

    public function insiteAction()
    {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

        //Get current layout state
        $this->loadLayout();          
 
		$order_nro = $this->getRequest()->getParam('order');
		$order = Mage::getSingleton('sales/order')->loadByIncrementId($order_nro);

		$apyn = $order->getBillingAddress()->getFirstname() . " " . $order->getBillingAddress()->getLastname();
		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'formulariocustom2',
			array('template' => 'modulodepago2/formulariocustom.phtml')
		)->setData('requestkey',$this->getRequest()->getParam('requestKey'))
		 ->setData('merchant',Mage::helper('modulodepago2/ambiente')->get_merchant())
		 ->setData('amount',number_format($order->getGrandTotal(), 2, ".", ""))
		 ->setData('mail',$order->getCustomerEmail())
		 ->setData('nombre',$apyn)
		 ->setData('orden', $order_nro);

		$this->getLayout()->getBlock('root')->setTemplate("page/1column.phtml");
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
    }

	public function whitepageAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("Formulario de pago"));
		$this->renderLayout();
	}
}