<?php
class Todopago_Modulodepago2_Block_Sales_Order_Creditmemo_Totals extends Mage_Sales_Block_Order_Creditmemo_Totals
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
		if ((float) $this->getSource()->getAdjustmentPositive()) {
			$total = new Varien_Object(array(
					'code'  => 'adjustment_positive',
					'value' => $this->getSource()->getAdjustmentPositive(),
					'label' => $this->__('Adjustment Refund')
			));
			$this->addTotal($total);
		}
		if ((float) $this->getSource()->getAdjustmentNegative()) {
			$total = new Varien_Object(array(
					'code'  => 'adjustment_negative',
					'value' => $this->getSource()->getAdjustmentNegative(),
					'label' => $this->__('Adjustment Fee')
			));
			$this->addTotal($total);
		}
		/**
		 <?php if ($this->getCanDisplayTotalPaid()): ?>
		 <tr>
		 <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Paid') ?></strong></td>
		 <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalPaid()) ?></strong></td>
		 </tr>
		 <?php endif; ?>
		 <?php if ($this->getCanDisplayTotalRefunded()): ?>
		 <tr>
		 <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Refunded') ?></strong></td>
		 <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalRefunded()) ?></strong></td>
		 </tr>
		 <?php endif; ?>
		 <?php if ($this->getCanDisplayTotalDue()): ?>
		 <tr>
		 <td colspan="6" class="a-right"><strong><?php echo $this->__('Total Due') ?></strong></td>
		 <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalDue()) ?></strong></td>
		 </tr>
		 <?php endif; ?>
		 */
		return $this;
	}
}
