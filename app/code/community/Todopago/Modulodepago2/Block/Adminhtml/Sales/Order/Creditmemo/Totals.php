<?php

class Todopago_Modulodepago2_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals {

    protected function _initTotals() {
        parent::_initTotals();

        $this->addTotal(new Varien_Object(array(
          'code' => 'todopago_costofinanciero',
          'value' => $this->getSource()->getOrder()->getTodopagoCostofinanciero(),
          'base_value' => $this->getSource()->getOrder()->getTodopagoCostofinanciero(),
          'label' => $this->helper('sales')->__('Otros cargos')
          )));



        $this->_totals['grand_total'] = new Varien_Object(array(
            'code' => 'grand_total',
            'strong' => true,
            'value' => $this->getSource()->getOrder()->getGrandTotal(),
            'base_value' => $this->getSource()->getOrder()->getBaseGrandTotal(),
            'label' => $this->helper('sales')->__('Grand Total'),
            'area' => 'footer'
        ));


        return $this;
    }

}
