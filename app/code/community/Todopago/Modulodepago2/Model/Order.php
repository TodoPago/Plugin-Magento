<?php

class Todopago_Modulodepago2_Model_Order {

	public function saveTodopagoCostoFinanciero(Varien_Event_Observer $observer) {
         $order = $observer -> getEvent() -> getOrder();
         $quote = $observer -> getEvent() -> getQuote();
         $shippingAddress = $quote -> getShippingAddress();
         if($shippingAddress && $shippingAddress -> getData('todopago_costofinanciero')){
             $order -> setData('todopago_costofinanciero', $shippingAddress -> getData('todopago_costofinanciero'));
             }
        else{
             $billingAddress = $quote -> getBillingAddress();
             $order -> setData('todopago_costofinanciero', $billingAddress -> getData('todopago_costofinanciero'));
             }
         $order -> save();
	}

    public function saveTodopagoCostoFinancieroMulti(Varien_Event_Observer $observer)
    {
         $order = $observer -> getEvent() -> getOrder();
         $address = $observer -> getEvent() -> getAddress();
         $order -> setData('todopago_costofinanciero', $shippingAddress -> getData('todopago_costofinanciero'));
         $order -> save();
    }

	public function adminorder(Varien_Event_Observer $observer)  {
		$order = $observer["order"];

		if($order->getPayment()->getMethodInstance()->getCode() == "modulodepago2") {
			$status = Mage::getStoreConfig('payment/modulodepago2/order_status');
			if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_status');        
			$order->setState("new", $status, "");
		}
	}
}