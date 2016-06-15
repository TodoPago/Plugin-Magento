<?php
abstract class Todopago_Modulodepago2_Model_Cybersource_Cybersource extends Mage_Core_Model_Abstract{

	protected $order;
	private $customer;

	public function __construct($order, $customer){
		$this->order = $order;
		$this->customer = $customer;
		Mage::log("constructor del CS: ".$this->order->getCustomerEmail());
	}

	public function getDataCS(){
		$datosCS = $this->completeCS();
		return array_merge($datosCS, $this->completeCSVertical());
	}

	private function completeCS(){
		$payDataOperacion = array();
		$billingAdress = $this->order->getBillingAddress();
		
		$payDataOperacion ['CSBTCITY'] = $this->getField($billingAdress->getCity());
		$payDataOperacion ['CSBTCOUNTRY'] = substr($this->getField($billingAdress->getCountry()),0,2);
		$payDataOperacion ['CSBTCUSTOMERID'] = $this->getField($billingAdress->getCustomerId());

		if($payDataOperacion ['CSBTCUSTOMERID']=="" or $payDataOperacion ['CSBTCUSTOMERID']==null)
		{
			$payDataOperacion ['CSBTCUSTOMERID']= "guest".date("ymdhs");
		}
		$payDataOperacion ['CSBTIPADDRESS'] = $this->getField($this->order->getRemoteIp());
		$payDataOperacion ['CSBTEMAIL'] = $this->getField($billingAdress->getEmail());
		$payDataOperacion ['CSBTFIRSTNAME'] = $this->getField($billingAdress->getFirstname());
		$payDataOperacion ['CSBTLASTNAME'] = $this->getField($billingAdress->getLastname());
		$payDataOperacion ['CSBTPOSTALCODE'] = $this->getField($billingAdress->getPostcode());
		$payDataOperacion ['CSBTPHONENUMBER'] = $this->getField($billingAdress->getTelephone());
		$payDataOperacion ['CSBTSTATE'] =  strtoupper(substr($this->getField($billingAdress->getRegion()),0,1));
		$payDataOperacion ['CSBTSTREET1'] = $this->getField($billingAdress->getStreet1());
		$payDataOperacion ['CSBTSTREET2'] = $this->getField($billingAdress->getStreet2());
		$payDataOperacion ['CSPTCURRENCY'] = 'ARS';//$this->getField($this->order->getBaseCurrencyCode());
		$payDataOperacion ['CSPTGRANDTOTALAMOUNT'] = number_format($this->order->getGrandTotal(), 2, ".", "");
		$payDataOperacion ['CSMDD6'] = Mage::getStoreConfig('payment/modulodepago2/cs_canaldeventa');
		$date = Mage::getModel('core/date');
		$fecha_1 = date('d-m-Y', $date->timestamp($this->customer->getCreatedAt()));
		$fecha_2 = date('d-m-Y', $date->timestamp(Mage::app()->getLocale()->date()));
		$payDataOperacion ['CSMDD7'] = Mage::helper('modulodepago2/data')->diasTranscurridos($fecha_1, $fecha_2);
		if($this->order->getCustomerIsGuest()){
			$payDataOperacion ['CSMDD8'] = "N";
		} else{
			$payDataOperacion ['CSMDD9'] = $this->customer->getPasswordHash();
		}

		if(!$this->customer->getCelular()){
			$payDataOperacion ['CSMDD11'] = $payDataOperacion['CSBTPHONENUMBER'];
		} else{
			$payDataOperacion ['CSMDD11'] = $this->getField($this->customer->getCelular());
		}

		return $payDataOperacion;

	}

	private function _sanitize_string($string){
		$string = htmlspecialchars_decode($string);

		$re = "/\\[(.*?)\\]|<(.*?)\\>/i";
		$subst = "";
		$string = preg_replace($re, $subst, $string);

		$replace = array("!","'","\'","\"","  ","$","\\","\n","\r",
			'\n','\r','\t',"\t","\n\r",'\n\r','&nbsp;','&ntilde;',".,",",.","+", "%", "-", ")", "(", "°");
		$string = str_replace($replace, '', $string);

		$cods = array('\u00c1','\u00e1','\u00c9','\u00e9','\u00cd','\u00ed','\u00d3','\u00f3','\u00da','\u00fa','\u00dc','\u00fc','\u00d1','\u00f1');
		$susts = array('Á','á','É','é','Í','í','Ó','ó','Ú','ú','Ü','ü','Ñ','ñ');
		$string = str_replace($cods, $susts, $string);

		$no_permitidas= array ("À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
		$permitidas= array    ("A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
		$string = str_replace($no_permitidas, $permitidas ,$string);

		return $string;
	}

	protected function getMultipleProductsInfo(){
		$payDataOperacion = array();
		//$id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        //$order = Mage::getModel('sales/order')->load($id);
        //$productos = $order->getAllItems();
		$productos = $this->order->getItemsCollection();
        //var_dump($productos);
        ///datos de la orden separados con #
		$productcode_array = array();
		$description_array = array();
		$name_array = array();
		$sku_array = array();
		$totalamount_array = array();
		$quantity_array = array();
		$price_array = array();

		foreach($productos as $item){
			if ($item->getParentItem()) continue;
			
			$p = Mage::getModel('catalog/product')->load($item->getProductId());
/////
			$cats = $p->getCategoryIds();

			if(count($cats) > 0) {
				$cat_id = $cats[0];
				$category = Mage::getModel('catalog/category')->load($cat_id);

				if ($category->getName()){
					$productcode_array[] = $category->getName();
				} else {
					$productcode_array[] = "default";
				}				
			} else {
				$productcode_array[] = "default";
			}
////
			
			$_description = $p->getName() . " " . $p->getDescription() . "  " . $p->getShortDescription();
			$_description = $this->getField($_description);
			$_description = trim($_description);
			$_description = substr($_description, 0,15);
			$description_array [] = str_replace("#","",$_description);

			$product_name = $item->getName();
			$name_array [] = $product_name;

			$sku = $item->getSku();
			$sku_array [] = $this->getField($sku);

			$product_quantity = $item->getQtyOrdered();
			$product_price = number_format($item->getPrice(),2, ".", "");
			$product_amount = number_format($product_quantity * $product_price, 2, ".", "");
			$totalamount_array[] = $product_amount;

			$quantity_array [] = intval($product_quantity);

			$price_array [] = $product_price;

		}
		$payDataOperacion ['CSITPRODUCTCODE'] = join('#', $productcode_array);
		$payDataOperacion ['CSITPRODUCTDESCRIPTION'] = join("#", $description_array);
		$payDataOperacion ['CSITPRODUCTNAME'] = join("#", $name_array);
		$payDataOperacion ['CSITPRODUCTSKU'] = join("#", $sku_array);
		$payDataOperacion ['CSITTOTALAMOUNT'] = join("#", $totalamount_array);
		$payDataOperacion ['CSITQUANTITY'] = join("#", $quantity_array);
		$payDataOperacion ['CSITUNITPRICE'] = join("#", $price_array);
		return $payDataOperacion;
	}

	public function getField($datasources){
		$return = "";
		try{

			$return = $this->_sanitize_string($datasources);

		}catch(Exception $e){
			Mage::log("Modulo de pago - TodoPago ==> operation_id:  $this->order->getIncrementId() - 
				no se pudo agregar el campo: Exception: $e");
		}

		return $return;
	}

	protected abstract function getCategoryArray($productId);
	protected abstract function completeCSVertical();

}
