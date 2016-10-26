<?php

class Todopago_Modulodepago2_Model_Order {

	public function adminorder(Varien_Event_Observer $observer)  {
		$order = $observer["order"];

		if($order->getPayment()->getMethodInstance()->getCode() == "modulodepago2") {
			$status = Mage::getStoreConfig('payment/modulodepago2/order_status');
			if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_status');        
			$order->setState("new", $status, "");
		}
	}
}