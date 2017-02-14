<?php
class Todopago_Modulodepago2_Block_Adminhtml_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Order_Invoice_Totals
{
	protected function _initTotals()
	{
		parent::_initTotals();
		$this->addTotal(new Varien_Object(array(
				'code'      => 'todopago_costofinanciero',
				'value'     => $this->getSource()->getOrder()->getTodopagoCostofinanciero(),
				'base_value'=> $this->getSource()->getOrder()->getTodopagoCostofinanciero(),
				'label'     => $this->helper('sales')->__('Otros cargos')
		)));
		return $this;
	}
}
			