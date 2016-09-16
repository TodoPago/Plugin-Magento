<?php
class Todopago_Modulodepago2_Helper_Connector extends Mage_Core_Helper_Abstract
{

	public function getConnector($modo = null) {

		require_once(Mage::getBaseDir('lib') . '/metododepago2/vendor/autoload.php');
		
		$http_header = $this->getHeader($modo);
		$mode = $this->getModo($modo);
			
		$todopago_connector = new TodoPago\Sdk($http_header, $mode);
		
		$proxyhost = Mage::getStoreConfig('payment/modulodepago2/proxyhost');
		if(empty($proxyhost)) $proxyhost = Mage::getStoreConfig('payment/todopago_servicio/proxyhost');
		$proxyport = Mage::getStoreConfig('payment/modulodepago2/proxyport');
		if(empty($proxyport)) $proxyport = Mage::getStoreConfig('payment/todopago_servicio/proxyport');
		$proxypass = Mage::getStoreConfig('payment/modulodepago2/proxypassword');
		if(empty($proxypass)) $proxypass = Mage::getStoreConfig('payment/todopago_servicio/proxypassword');
		$proxyuser = Mage::getStoreConfig('payment/modulodepago2/proxyuser');
		if(empty($proxyuser)) $proxyuser = Mage::getStoreConfig('payment/todopago_servicio/proxyuser');
		
		if(!empty($proxyhost) && !empty($proxyport))
			$todopago_connector->setProxyParameters($proxyhost, $proxyport, $proxyuser, $proxypass);

		return $todopago_connector;
	}

	public function getModo($modo = null) {
		if($modo != null) return $modo;

		if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
			return "test";
		}
		return "prod";
	}
	
	public function getHeader() {
		$modo = $this->getModo();
		if($modo == "test") {
			$config_header = Mage::getStoreConfig('payment/modulodepago2/apikey_test');
		    if(empty($config_header)) $config_header = Mage::getStoreConfig('payment/todopago_modo/apikey_test');
		} else {
			$config_header = Mage::getStoreConfig('payment/modulodepago2/apikey');
			if(empty($config_header)) $config_header = Mage::getStoreConfig('payment/todopago_modo/apikey');
		}
		if(empty($config_header)) {
			$config_header = Mage::getStoreConfig('payment/modulodepago2/header_http');
		}
		$header = json_decode($config_header, TRUE);
		if($header == null) {
			$header = array("Authorization" => $config_header);
		}
        return $header;
    }
}