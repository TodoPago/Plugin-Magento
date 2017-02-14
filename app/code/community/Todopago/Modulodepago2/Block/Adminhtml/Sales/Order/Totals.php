<?php
class Todopago_Modulodepago2_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{

	protected function _initTotals()
	{
		Mage::log(__METHOD__);
		parent::_initTotals();
		$this->_totals['todopago_costofinanciero'] = new Varien_Object(array(
				'code'      => 'todopago_costofinanciero',

				'value'     => $this->getSource()->getTodopagoCostofinanciero(),
				'base_value'=> $this->getSource()->getTodopagoCostofinanciero(),
				'label'     => $this->helper('sales')->__('Otros cargos'),

		));



		return $this;
	}
}
