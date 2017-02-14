<?php
class Todopago_Modulodepago2_Model_Order_Creditmemo_Total_Todopago extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo) {

        return $this;

        $order = $creditmemo->getOrder();
        $todopago_cf = $order->getTodopagoCostofinanciero();

        if ($todopago_cf) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$todopago_cf);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$todopago_cf);
        }

        return $this;
    }
}

