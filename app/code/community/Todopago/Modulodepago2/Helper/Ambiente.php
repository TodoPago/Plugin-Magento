<?php
class Todopago_Modulodepago2_Helper_Ambiente extends Mage_Core_Helper_Abstract
{
	public function get_merchant(){
		$_merchant="";
		if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
			$_merchant = Mage::getStoreConfig('payment/modulodepago2/idstore_test');
			if(empty($_merchant)) $_merchant = Mage::getStoreConfig('payment/todopago_modo/idstore_test');
		} else{
			$_merchant = Mage::getStoreConfig('payment/modulodepago2/idstore');
			if(empty($_merchant)) $_merchant = Mage::getStoreConfig('payment/todopago_modo/idstore');
		}
		return $_merchant;
	}

	public function get_security_code(){
		$_security_code ="";
		if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
			$_security_code = Mage::getStoreConfig('payment/modulodepago2/codigo_seguridad_test');
			if(empty($_security_code)) $_security_code = Mage::getStoreConfig('payment/todopago_modo/codigo_seguridad_test');
		} else{
			$_security_code = Mage::getStoreConfig('payment/modulodepago2/codigo_seguridad');
			if(empty($_security_code)) $_security_code = Mage::getStoreConfig('payment/todopago_modo/codigo_seguridad');
		}

		return $_security_code;
	}
}