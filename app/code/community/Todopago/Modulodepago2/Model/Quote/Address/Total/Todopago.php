<?php
class Todopago_Modulodepago2_Model_Quote_Address_Total_Todopago extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    
    public function __construct() {
         $this -> setCode('todopago_costofinanciero');
    }
    
    public function collect(Mage_Sales_Model_Quote_Address $address) {
         parent :: collect($address);
         $items = $this->_getAddressItems($address);
         if (!count($items)) {
            return $this;
         }
         $quote= $address->getQuote();

         $todopago_cf = 0.00;

         $todopago_cf = $quote -> getStore() -> roundPrice($todopago_cf);
         $this -> _setAmount($todopago_cf) -> _setBaseAmount($todopago_cf);
         $address->setData('todopago_costofinanciero',$todopago_cf);

         return $this;
     }
   
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
         parent :: fetch($address);
         $amount = $address -> getTotalAmount($this -> getCode());
         if ($amount != 0){
             $address -> addTotal(array(
                     'code' => $this -> getCode(),
                     'title' => $this -> getLabel(),
                     'value' => $amount
                    ));
         }
       
         return $this;
     }
   
    public function getLabel() {
         return 'Otros cargos';
    }
}