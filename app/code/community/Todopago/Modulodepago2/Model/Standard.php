<?php

class Todopago_Modulodepago2_Model_Standard extends Mage_Payment_Model_Method_Abstract{

	protected $_code = 'modulodepago2';
	protected $_formBlockType = 'modulodepago2/formstandard';
	protected $_infoBlockType = 'modulodepago2/infostandard';
	protected $_isGateway = true;
	protected $_canCapturePartial = true;
	protected $_canRefund = true;
	protected $_canRefundInvoicePartial = true;
	protected $_canCapture = true;
	protected $_isInitializeNeeded = true;
	
    public function initialize($paymentAction, $stateObject)
    {
		$status = Mage::getStoreConfig('payment/modulodepago2/order_status');
		if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_status');        

        $state = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject->setState($state);
        $stateObject->setStatus($status);
        $stateObject->setIsNotified(false);
    }

    public function assignData($data) {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $this->tpcheckout = $data->getTpcheckout();
        return $this;
    }

	public function getOrderPlaceRedirectUrl(){
		Mage::log("init :".__METHOD__);
		return Mage::getUrl('modulodepago2/payment/getPayData', array(
				'_secure' => true,
				'tpcheckout' => $this->tpcheckout
			));
	}

	public function refund(Varien_Object $payment, $amount){
        $order = $payment->getOrder();
		
		$todopago_connector = Mage::helper('modulodepago2/connector')->getConnector();
		
        $requestkey = $order->getTodopagoclave();
		Mage::log("Modulo de pago - Todopago ==> DEVOLUCION");
		
		$returnData = array(
			"Security" => Mage::helper('modulodepago2/ambiente')->get_security_code(),
			"Merchant" => Mage::helper('modulodepago2/ambiente')->get_merchant(),
			"RequestKey" => $requestkey,
			"AMOUNT" => number_format($amount,2,".",""),
		); 
		Mage::log("Modulo de pago - Todopago ==> Request: " . json_encode($returnData));
        $result = $todopago_connector->returnRequest($returnData);
		
		Mage::log("Modulo de pago - Todopago ==> Response: " . json_encode($result));
        if($result['StatusCode'] != 2011) {
            $errorCode = 'Invalid Data';
            $errorMsg = 'Error Processing the request';
            Mage::throwException($errorMsg);
        }
        return $this;

    }
}
