<?php
class Todopago_Modulodepago2_Block_Sales_Order_Invoice_Totals extends Mage_Sales_Block_Order_Invoice_Totals
{
	
	protected function _initTotals()
	{
		parent::_initTotals();
		////
		$this->_totals['todopago_costofinanciero'] = new Varien_Object(array(
				'code'  => 'todopago_costofinanciero',
				'value' => $this->getSource()->getOrder()->getTodopagoCostofinanciero(),
				'label' => $this->__('Otros cargos'),
				 
		));
		////
		$this->removeTotal('base_grandtotal');
		return $this;
	}
}
			