<?php
define('MAX_CUOTAS_DEFAULT', '12');

class Todopago_Modulodepago2_PaymentController extends Mage_Core_Controller_Front_Action{

    public function notificationAction() {
        Mage::log("init: ".__METHOD__);
        $order_id = $this->getRequest()->get('Order');
        $answer_key = $this->getRequest()->get('Answer');

        $todopagotable = new Todopago_Modulodepago2_Model_Todopagotable();
        $transaccion = $todopagotable->load($order_id,"order_id");

        if($transaccion->getAnswer_key() == null) {
            return $this->getResponse()->setHeader('HTTP/1.1','404',true)->sendResponse();
        }

        if($transaccion->getAnswer_key() != $answer_key) {
            return $this->getResponse()->setHeader('HTTP/1.1','400',true)->sendResponse();
        }

        //Actualizar order status
        echo "OK";
    }

    // este m�todo recolecta los datos b�sicos para realizar la primera transacci�n con el gateway
    public function getPayDataAction(){
        Mage::log("init: ".__METHOD__);

        $magento_version = Mage::getVersion();
        $php_version = phpversion();
        Mage::log("[Mag:$magento_version - php: $php_version] " );


        $id = Mage::getSingleton('checkout/session')
        ->getLastRealOrderId();

        Mage::log(__METHOD__." OPERATIONID: ".$id);

        $order = Mage::getSingleton('sales/order')
        ->loadByIncrementId($id);

        $message = "";

$status = Mage::getStoreConfig('payment/modulodepago2/order_status');
if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_status');
$order->setState("new", $status, "");

        $productos = $order->getItemsCollection();

        $customer_id = $order->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customer_id);

        Mage::log("Modulo de pago - Todopago ==> {order_id: $id, customer_id: $customer_id");
        $vertical = Mage::getStoreConfig('payment/modulodepago2/cs_verticales');
        $payDataComercial = array();

        Mage::log("orden desde el controller: ".$order->getCustomerEmail());

        $payDataOperacion = Todopago_Modulodepago2_Model_Cybersource_Factorycybersource::get_cybersource_extractor($vertical,
            $order, $customer)->getDataCS();

        // datos b�sicos para tipo de operaciones de pago
        // Merchant
        $payDataComercial ['URL_OK'] = Mage::getBaseUrl().'modulodepago2/payment/secondStep?Order='.$order->getIncrementId();
        $payDataComercial ['URL_ERROR'] = Mage::getBaseUrl().'modulodepago2/payment/secondStep?Order='.$order->getIncrementId();

        $payDataComercial ['Merchant'] = Mage::helper('modulodepago2/ambiente')->get_merchant();
        $payDataComercial ['Security'] = Mage::helper('modulodepago2/ambiente')->get_security_code();

        // EncodingMethod
        $payDataComercial ['EncodingMethod'] = 'XML';
        // NROCOMERCIO
        $payDataOperacion['MERCHANT'] = Mage::helper('modulodepago2/ambiente')->get_merchant();
        // NROOPERACION
        $payDataOperacion ['OPERATIONID'] = $order->getIncrementId();
        // MONTO
        $payDataOperacion ['AMOUNT'] = number_format($order->getGrandTotal(), 2, ".", "");
        //CURRENCY CODE
        $payDataOperacion ['CURRENCYCODE'] = "032";
        // EMAILCLIENTE (puede ser null)
        $payDataOperacion ['EMAILCLIENTE'] = $order->getCustomerEmail();

        if ( Mage::getStoreConfig('payment/modulodepago2/cuotas_enabled') ){
            $maxCuotas = Mage::getStoreConfig('payment/modulodepago2/cuotas');
            $payDataOperacion ['MAXINSTALLMENTS'] = ( !is_null($maxCuotas) && $maxCuotas > 0 && is_numeric($maxCuotas))? $maxCuotas:MAX_CUOTAS_DEFAULT;
        }

        $timeout = Mage::getStoreConfig('payment/modulodepago2/timeout');
        if(!empty($timeout)){
            $payDataOperacion ['TIMEOUT'] = Mage::getStoreConfig('payment/modulodepago2/timeout_ms');
        }

        //plugin information
        $payDataOperacion ['ECOMMERCENAME'] = "MAGENTO";
        $payDataOperacion ['ECOMMERCEVERSION'] = Mage::getVersion();
        $payDataOperacion ['CMSVERSION'] = "";
        
        if(Mage::getStoreConfig('payment/modulodepago2/hibrido') == 1) {
            $formMode = "H";
        }else{
            $formMode = "E";
        }

        $payDataOperacion ['PLUGINVERSION'] = (string)Mage::getConfig()->getNode('modules/Todopago_Modulodepago2/version')."-".$formMode;
        
        $this->firstStep($payDataComercial, $payDataOperacion);

    }

    public function setCelularAction()
    {
        Mage::log("init: ".__METHOD__);
        $quote_id = $this->getRequest()->get($quote_id);
        $celular = $this->getRequest()->get($celular);
        $checkout = Mage::getSingleton('checkout/session')->getQuote();
        $checkout->getBillingAddress()->setData('celular', $celular);
        $checkout->save();
    }

    public function firstStep($payDataComercio, $payDataOperacion){
        /*COMENTO LAS PARTES DONDE SE UTILIZAN LOS ESTADOS CREADOS POR EL MODULO*/
        Mage::log("init: ".__METHOD__);

        $order = new Mage_Sales_Model_Order ();
        $order->loadByIncrementId($payDataOperacion ['OPERATIONID']);
        $message = "";

        try{

            Mage::log(__METHOD__."<-- try - OPERATIONID: ".$payDataOperacion ['OPERATIONID']);
            $todopago_connector = Mage::helper('modulodepago2/connector')->getConnector();

            Mage::log("Modulo de pago - TodoPago ==> payDataComercio --> ".json_encode($payDataComercio));

            // si esta habilitado para la direcciones de gmaps setear cliente google
/*            $address_result = $this->address_loaded($payDataOperacion);
            $gClient = null;

            if($address_result['address_loaded']){
                $payDataOperacion = $address_result['payDataOperacion'];
            }elseif (Mage::getStoreConfig('payment/modulodepago2/gmaps') == 1){
                $gClient = new \TodoPago\Client\Google();
                if($gClient != null) {
                    $todopago_connector->setGoogleClient($gClient);
                }
            }
*/
            Mage::log("Modulo de pago - TodoPago ==> payDataOperacion --> ".json_encode($payDataOperacion));

            $first_step = $todopago_connector->sendAuthorizeRequest($payDataComercio, $payDataOperacion);
            //guardo direccion 
/*          if($gClient != null) {
                $this->tp_save_address($todopago_connector->getGoogleClient()->getFinalAddress());
                // modify addresses
                $order = $this->update_addresses($order, $payDataOperacion);

            }
*/

            if($first_step["StatusCode"] == 702){
                Mage::log("Modulo de pago - TodoPago ==> respuesta de sendAuthorizeRequest --> reintento SAR");
                $first_step = $todopago_connector->sendAuthorizeRequest($payDataComercio, $payDataOperacion);
            }


            Mage::log("Modulo de pago - TodoPago ==> respuesta de sendAuthorizeRequest -->".json_encode($first_step));

            $todopagotable = new Todopago_Modulodepago2_Model_Todopagotable();
            $todopagotable->setOrderId($payDataOperacion ['OPERATIONID']);

            $todopagotable->setSendauthorizeanswerStatus(Mage::getModel('core/date')->date('Y-m-d H:i:s')." - ".$first_step["StatusCode"]." - ".$first_step['StatusMessage']);

            if($first_step["StatusCode"] == -1){
                Mage::log("StatusCode = -1 - OPERATIONID".$payDataOperacion ['OPERATIONID']);
                $todopagotable->setRequestKey($first_step['RequestKey']);
                $todopagotable->save();
                $order->setData('todopagoclave', $first_step['RequestKey']);

                $status = Mage::getStoreConfig('payment/modulodepago2/order_status');
                if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_status');
                $state = $this->_get_new_order_state($status);

                if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                    $message = "Todo Pago (TEST): " . $first_step['StatusMessage'];
                }else
                {
                    $message = "Todo Pago: " . $first_step['StatusMessage'];
                }

                $order->setState($state, $status, $message);

                $order->save();
                Mage::log("Modulo de pago - TodoPago ==> redirige a: ".$first_step['URL_Request']);
                if(Mage::getStoreConfig('payment/modulodepago2/hibrido') == 1) {
                    if($this->getRequest()->get('tpcheckout') == "Mage_Checkout") {

                        $url = Mage::getUrl('modulodepago2/formulariocustom/index', array(
                            '_secure' => true,
                            'order' =>  $order->getIncrementId(),
                            'requestKey' => $first_step['PublicRequestKey'],
                        ));
                        // echo '{"url":"'.$url.'"}';
                        return $this->replyJson(array('url' => $url));
                    } else {
                        $url = Mage::getUrl('modulodepago2/formulariocustom/insite', array(
                            '_secure' => true,
                            'order' =>  $order->getIncrementId(),
                            'requestKey' => $first_step['PublicRequestKey'],
                        ));
                        $this->_redirectUrl($url);
                        return;
                    }
                }
                $this->_redirectUrl($first_step['URL_Request']);

            } else{
                Mage::log("StatusCode != -1 - OPERATIONID".$payDataOperacion ['OPERATIONID']);

                $status = Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
                if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
                $state = $this->_get_new_order_state($status);
                if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                    $message = "Todo Pago (TEST): " . $first_step['StatusMessage'];

                } else{
                    $message = "Todo Pago: " . $first_step['StatusMessage'];
                }

                $order->cancel();
                Mage::log("Orden cancelada");
                $order->setState($state, $status, $message);
                $order->save();
                Mage::log("Modulo de pago - TodoPago ==> redirige a: checkout/onepage/failure");

                $this->emptyCart($order);

                if(Mage::getStoreConfig('payment/modulodepago2/hibrido') == 1) {
                    $this->getResponse()->clearHeaders()->setHeader('HTTP/1.0', 400, true);
                    $url = Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
                    return $this->replyJson(array('url' => $url));
                } else {
                    $url = Mage::getUrl('checkout/onepage/failure', array(
                        '_secure' => true
                    ));
                    $this->_redirectUrl($url);
                    return;
                }
            }

        } catch(Exception $e){
            Mage::log("catch : ".__METHOD__);
            Mage::log("Modulo de pago - TodoPago - OPERATIONID: ".$payDataOperacion['OPERATIONID']."==> (Exception)".json_encode($e));


            $status = Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
            $state = $this->_get_new_order_state($status);

            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST)(Exception): " . $e;
            } else{
                $message = "Modulo de pago - TodoPago ==> (Exception)" . $e;
            }

                        $order->cancel();
                        Mage::log("Orden cancelada");
            $order->setState($state, $status, $message);
            $order->save();
            Mage::log("Modulo de pago - TodoPago ==> redirige a: checkout/onepage/failure");

            $this->emptyCart($order);

            if(Mage::getStoreConfig('payment/modulodepago2/hibrido') == 1) {
                $this->getResponse()->clearHeaders()->setHeader('HTTP/1.0', 400, true);
                $url = Mage::getUrl('checkout/onepage/failure', array('_secure' => true));
                return $this->replyJson(array('url' => $url));
            } else {
                $url = Mage::getUrl('checkout/onepage/failure', array(
                    '_secure' => true
                ));
                $this->_redirectUrl($url);
                return;
            }
    }
}

private function update_addresses(&$order,  $payDataOperacion){

    $billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddress()->getId());
    $billingAddress
    ->setStreet($payDataOperacion['CSBTSTREET1'])
    ->setCity($payDataOperacion['CSBTCITY'])
    ->setCountry_id($payDataOperacion['CSBTCOUNTRY'])
    ->setPostcode($payDataOperacion['CSBTPOSTALCODE'])
    ->save(); 

    $shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddress()->getId());
    $shippingAddress
    ->setStreet($payDataOperacion['CSSTSTREET1'])
    ->setCity($payDataOperacion['CSSTCITY'])
    ->setCountry_id($payDataOperacion['CSSTCOUNTRY'])
    ->setPostcode($payDataOperacion['CSSTPOSTALCODE'])
    ->save();

    $order->setShippingAddress($shippingAddress);
    $order->setBillingAddress($shippingAddress);
    $order->save();
    return $order; 
}

private function address_loaded($payDataOperacion){

    $CSBT_address = $this->get_loaded_address($payDataOperacion, 'CSBT');
    $CSST_address = $this->get_loaded_address($payDataOperacion, 'CSST');
   

    if (($CSBT_address != null) && ( $CSST_address != null ) ){
        $payDataOperacion['CSBTSTREET1']= $CSBT_address[0]['address'];
        $payDataOperacion['CSBTPOSTALCODE']= $CSBT_address[0]['postal_code'];
        $payDataOperacion['CSBTCITY']= $CSBT_address[0]['city'];
        $payDataOperacion['CSBTCOUNTRY']= $CSBT_address[0]['country'];

        $payDataOperacion['CSSTSTREET1']= $CSST_address[0]['address'];
        $payDataOperacion['CSSTPOSTALCODE']= $CSST_address[0]['postal_code'];
        $payDataOperacion['CSSTCITY']= $CSST_address[0]['city'];
        $payDataOperacion['CSSTCOUNTRY']= $CSST_address[0]['country'];

        $address_loaded = true;
    }else{
        $address_loaded = false;
    }

    $address_result = array('payDataOperacion' => $payDataOperacion,                            
                            'address_loaded' => $address_loaded
                            );

    return $address_result; 
}


private function get_loaded_address($payDataOperacion, $type){
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
   
    $street  = explode(' ', $payDataOperacion["{$type}STREET1"]);

    $where = '';  
    foreach ($street as $val) { 
        $where .= " address like '%{$val}%' and ";
    }

    $query = 'SELECT * FROM ' . $resource->getTableName('todopagoaddress') . " where {$where} postal_code like '%{$payDataOperacion["{$type}POSTALCODE"]}%' and country='{$payDataOperacion["{$type}COUNTRY"]}'" ;

    return $readConnection->fetchAll($query);
}


private function  tp_save_address($payDataOperacion){

    $resource = Mage::getSingleton('core/resource');

    $writeConnection = $resource->getConnection('core_write');

    $table = $resource->getTableName('todopagoaddress');

    $query = "INSERT INTO {$table} (address, city, postal_code, country) 
    VALUES ('{$payDataOperacion['billing']['CSBTSTREET1']}', '{$payDataOperacion['billing']['CSBTCITY']}', '{$payDataOperacion['billing']['CSBTPOSTALCODE']}', '{$payDataOperacion['billing']['CSBTCOUNTRY']}');";

    $writeConnection->query($query);

    if($payDataOperacion['billing']['CSBTSTREET1'] != $payDataOperacion['shipping']['CSSTSTREET1'] && 
       $payDataOperacion['billing']['CSBTCITY'] != $payDataOperacion['shipping']['CSSTCITY'] ){

        $query = "INSERT INTO {$table} (address, city, postal_code, country) 
        VALUES ('{$payDataOperacion['shipping']['CSSTSTREET1']}', '{$payDataOperacion['shipping']['CSSTCITY']}', '{$payDataOperacion['shipping']['CSSTPOSTALCODE']}', '{$payDataOperacion['shipping']['CSSTCOUNTRY']}');";

        $writeConnection->query($query);
    }

}


private function replyJson($values)
{
    return $this->getResponse()->setHeader('Content-Type', 'application/json')->appendBody(json_encode($values));
}

public function secondStepAction(){
    Mage::log("init: ".__METHOD__);
    $todopagotable = new Todopago_Modulodepago2_Model_Todopagotable();
    $todopagotable->load($this->getRequest()->get('Order'), "order_id");
    if($todopagotable->getAnswerKey() == null){
    if($this->getRequest()->get('Answer')) {
            $this->lastStep($this->getRequest()->get('Order'), $this->getRequest()->get('Answer'));
    } else if($this->getRequest()->get('Error')){
        $this->lastStep($this->getRequest()->get('Order'), null, $this->getRequest()->get('Error'));
    }
    }else{
        Mage_Core_Controller_Varien_Action::_redirect('/', array('_secure' => true));
    }
}

public function lastStep($order_key, $answer_key, $error = null){
    /*COMENTO LAS PARTES DONDE SE UTILIZAN LOS ESTADOS CREADOS POR EL MODULO*/
    Mage::log("init: ".__METHOD__);
    $todopago_connector = Mage::helper('modulodepago2/connector')->getConnector();

        // /a este metodo es al que me va a devolver el gateway en caso que todo salga ok
    Mage::log("Modulo de pago - TodoPago ==> secondStep - orderid: ".$order_key);
    Mage::log("Modulo de pago - TodoPago ==> secondStep - AnswerKey: ".$answer_key);
    $order = new Mage_Sales_Model_Order ();
    $order->loadByIncrementId($order_key);

    //merchant
    $merchant = Mage::helper('modulodepago2/ambiente')->get_merchant();

    // Security
    $security = Mage::helper('modulodepago2/ambiente')->get_security_code();

    $requestkey = $order->getTodopagoclave();

    if($error != null) {
            $status = Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
            $state = $this->_get_new_order_state($status);

            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST): " . $error;
            } else{
                $message = "Todo Pago: " . $error;
            }
                        $order->cancel();
                        Mage::log("Orden cancelada");
            $order->setState($state, $status, $message);
            $order->sendOrderUpdateEmail(true, $message);
            $order->save();
        Mage::getSingleton('core/session')->addError($error);
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
    }

    // ahora vuelvo a consumir web service para confirmar la transaccion
    $optionsAnswer = array(
        'Security' => $security,
        'Merchant' => $merchant,
        'RequestKey' => $requestkey,
        'AnswerKey' => $answer_key
        );

    Mage::log("Modulo de pago - TodoPago ==> secondStep (".$order_key.") - params GAA: ".json_encode($optionsAnswer));
    $message = "";

    try{
        Mage::log("try ".__METHOD__);
        $second_step = $todopago_connector->getAuthorizeAnswer($optionsAnswer);
        Mage::log("Modulo de pago - TodoPago ==> secondStep (".$order_key.") - response GAA: ".json_encode($second_step));

        $todopagotable = new Todopago_Modulodepago2_Model_Todopagotable();
        $todopagotable->load($order_key, "order_id");
        $todopagotable->setAnswerKey($answer_key);
        $todopagotable->setGetauthorizeanswerStatus(Mage::getModel('core/date')->date('Y-m-d H:i:s')." - ".$second_step["StatusCode"]." - ".$second_step['StatusMessage']);
        $todopagotable->save();

            //para saber si es un cupon
        if(strlen($second_step['Payload']['Answer']["BARCODE"]) > 0){
            $status = Mage::getStoreConfig('payment/modulodepago2/estado_offline');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_offline');
            $state = $this->_get_new_order_state($status);

            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST): " . $second_step['StatusMessage'];
            } else{
                $message = "Todo Pago: " . $second_step['StatusMessage'];
            }
            $order->setState($state, $status, $message);

            try{
                $order->sendNewOrderEmail();
            }catch(Exception $e){
                Mage::log("catch : ".__METHOD__);
                Mage::log("message: ".var_export($e, true));
                $order->sendOrderUpdateEmail(true, $message);
            }

            $order->save();
            Mage_Core_Controller_Varien_Action::_redirect('modulodepago2/cupon/index', array('_secure' => true,
               'nroop' => $order_key,
               'venc' => $second_step['Payload']['Answer']["COUPONEXPDATE"],
               'total' => $second_step['Payload']['Request']['AMOUNT'],
               'code' => $second_step['Payload']['Answer']["BARCODE"],
               'tipocode' => $second_step['Payload']['Answer']["BARCODETYPE"],
               'empresa' => $second_step['Payload']['Answer']["PAYMENTMETHODNAME"],
               ));
        }//caso de transaccion aprovada
        elseif($second_step['StatusCode'] == -1){
            $status = Mage::getStoreConfig('payment/modulodepago2/order_aprov');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/order_aprov');
            $state = $this->_get_new_order_state($status);
            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST): " . $second_step['StatusMessage'];
            } else{
                $message = "Todo Pago: " . $second_step['StatusMessage'];
            }
            $order->setState($state, $status, $message);

            /*costo financiero*/
            $amountBuyer = isset($second_step['Payload']['Request']['AMOUNTBUYER'])?$second_step['Payload']['Request']['AMOUNTBUYER']:number_format($order->getGrandTotal(), 2, ".", "");
            $cf = $amountBuyer - $order->getGrandTotal();

            $order->setTodopagoCostofinanciero($cf);
            $order->setGrandTotal($amountBuyer);
            $order->setBaseGrandTotal($amountBuyer);

            $payment = $order->getPayment();
            $payment->setTransactionId($second_step['AuthorizationKey']);
            $payment->setParentTransactionId($payment->getTransactionId());

            $payment->save();

            $invoice = $order->prepareInvoice()
                   ->setTransactionId(1)
                   ->addComment("Invoice created.")
                   ->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

            $invoice->setGrandTotal($amountBuyer);
            $invoice->setBaseGrandTotal($amountBuyer);

            $invoice = $invoice->register()
                   ->pay();

            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $order->save();

            try{
                $order->sendNewOrderEmail();
            }catch(Exception $e){
                Mage::log("catch : ".__METHOD__);
                Mage::log("message: ".var_export($e, true));
                $order->sendOrderUpdateEmail(true, $message);
            }

            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => true));
        }
        //caso de transaccion no aprobada
        elseif($second_step['StatusCode'] != -1){
            $status = Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
            $state = $this->_get_new_order_state($status);

            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST): " . $second_step['StatusMessage'];
            } else{
                $message = "Todo Pago: " . $second_step['StatusMessage'];
            }
                        $order->cancel();
                        Mage::log("Orden cancelada");
            $order->setState($state, $status, $message);
            $order->sendOrderUpdateEmail(true, $message);
            $order->save();

            $this->emptyCart($order);

        Mage::getSingleton('core/session')->addError($second_step['StatusMessage']);
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));            }
        }
        catch(Exception $e){
            Mage::log("catch : ".__METHOD__);

            $status = Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
            if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
            $state = $this->_get_new_order_state($status);
            if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
                $message = "Todo Pago (TEST)(Exception): " . $e;
            } else{
                $message = "Todo Pago (Exception): " . $e;
            }
            $order->cancel();
            Mage::log("Orden cancelada");
            $order->setState($state, $status, $message);
            $order->sendOrderUpdateEmail(true, $message);
            $order->save();

            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
        }
    }

    public function urlerrorAction(){
        Mage::log("init: ".__METHOD__);
        // este m�to es al que me dva a devolder el gateway en caso que algo salga mal
        $order_id = $this->getRequest()->get('Order');
        Mage::log(__METHOD__." Modulo de pago - TodoPago ==> urlerror - orderid: ".$order_id);
        $answer = $this->getRequest()->get('Answer');
        Mage::log(__METHOD__." Modulo de pago - TodoPago ==> urlerror - AnswerKey: ".$answer);

        $order = new Mage_Sales_Model_Order ();
        $order->loadByIncrementId($order_id);

        $status =Mage::getStoreConfig('payment/modulodepago2/estado_denegada');
        if(empty($status)) $status = Mage::getStoreConfig('payment/todopago_avanzada/estado_denegada');
        $state = $this->_get_new_order_state($status);

        $message = "";
        if(Mage::getStoreConfig('payment/modulodepago2/modo_test_prod') == "test"){
            $message = "Todo Pago (TEST): error en el pago del formulario";
        } else{
            $message = "OPERATIONID: $order_id - Todo Pago: error en el pago del formulario";
        }
                $order->cancel();
                Mage::log("Orden cancelada");
        $order->setState($state, $status, $message);
        $order->save();
        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
    }

    private function redirect_first($cart_array){
        Mage::log("init: ".__METHOD__);
        Mage_Core_Controller_Varien_Action::_redirect('modulodepago2/formulariocustom/insite',
          array('_secure' => true, 'amount'=>$cart_array['Amount'])
          );
    }

    protected function _get_new_order_state($status){
        Mage::log("init: ".__METHOD__);

        if(version_compare(Mage::getVersion(), "1.5.0.0") == -1) {
            return $status;
        }

        $statuses  = Mage::getResourceModel('sales/order_status_collection')->joinStates()->addFieldToFilter('main_table.status', $status)->addFieldToFilter('is_default', 1)->getFirstItem();
        if(count($statuses->getData()) == 0)
            $statuses  = Mage::getResourceModel('sales/order_status_collection')->joinStates()->addFieldToFilter('main_table.status', $status)->getFirstItem();
        $state = $statuses->getState();

        return $state;
    }

    private function emptyCart($order){

        if (empty(Mage::getStoreConfig('payment/modulodepago2/cart_enabled'))){
            $cart = Mage::getSingleton('checkout/cart');
            $items = $order->getItemsCollection();

            foreach ($items as $item) {
                try {
                    $cart->addOrderItem($item);
                } catch (Mage_Core_Exception $e){
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    }
                    else {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    }
                    
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException($e,
                        Mage::helper('checkout')->__('Can not add item to shopping cart')
                    );
                }
            }

            $cart->save();
        }

    }
}
