<?php

class Todopago_modulodepago2_Block_Adminhtml_Sales_Order_View_Tab_Getstatus extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface{


	protected $_chat = null;

	protected function _construct(){
		parent::_construct();
		$this->setTemplate('modulodepago2/sales/order/view/tab/getstatus.phtml');
	}

	public function getTabLabel(){
		return $this->__('Información de Pago (TodoPago)');
	}

	public function getTabTitle(){
		return $this->__('Información de Pago (TodoPago)');
	}

	public function canShowTab(){
		return true;
	}

	public function isHidden(){
		return false;
	}

	public function getOrder(){
		return Mage::registry('current_order');
	}

	public function getLastStatus(){
	
		$connector = Mage::helper('modulodepago2/connector')->getConnector();
		
		$order_id =  $this->getRequest()->get('order_id');
		$id = $this->getOrderIncrementId($order_id);

		if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
			$merchant = Mage::getStoreConfig('payment/modulodepago2/idstore_test');
			if(empty($merchant)) $merchant = Mage::getStoreConfig('payment/todopago_modo/idstore_test');
		} else{
			$merchant = Mage::getStoreConfig('payment/modulodepago2/idstore');
			if(empty($merchant)) $merchant = Mage::getStoreConfig('payment/todopago_modo/idstore');
		}

		try{
			$status = $connector->getStatus(array('MERCHANT'=>$merchant, 'OPERATIONID'=>$id));
			return $status;
		}
		catch(Exception $e){
			$exception['Operations']['Exception']="Error el consumir Web Service Todopago";
			return $exception;
		}
	}
	
	public function printGetStatus($arrayResult, $indent) {
		$rta = '';

		foreach ($arrayResult as $key => $value) {
		    if ($key !== 'nil' && $key !== "@attributes") {
			if (is_array($value) ){
			    $rta .= "<tr>";
			    $rta .= "<td>".str_repeat("-", $indent) . "<strong>$key:</strong></td>";
			    $rta .= "<td>".$this->printGetStatus($value, $indent + 2)."</td>";
			    $rta .= "</tr>";

			} else {
			    $rta .= "<tr><td>".str_repeat("-", $indent) . "<strong>$key:</strong></td><td> $value </td></tr>";
			}
		    }
		}
		return $rta;
	    }

	private  function getOrderIncrementId($order_id){
		$order = Mage::getModel("sales/order")->load($order_id)->getIncrementId();
		return $order; 
	}
}
