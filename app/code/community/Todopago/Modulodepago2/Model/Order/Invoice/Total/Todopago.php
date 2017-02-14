<?php
class Todopago_Modulodepago2_Model_Order_Invoice_Total_Todopago extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
        $order=$invoice->getOrder();
        $todopago_cf = $order->getTodopagoCostofinanciero();
        if ($todopago_cf&&count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal()+$todopago_cf);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$todopago_cf);
        }
        return $this;
    }
}