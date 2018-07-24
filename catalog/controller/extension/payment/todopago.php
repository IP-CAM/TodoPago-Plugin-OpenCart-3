<?php

require_once DIR_APPLICATION . 'controller/extension/todopago/vendor/autoload.php';
require_once DIR_APPLICATION . 'controller/extension/todopago/ControlFraude/includes.php';
require_once DIR_SYSTEM . 'library/todopago/Logger/loggerFactory.php';
require_once DIR_SYSTEM . 'library/todopago/todopago_ctes.php';
require_once DIR_APPLICATION . '../system/library/todopago/Billeterahelper.php';

class ControllerExtensionPaymentTodopago extends Controller
{

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->logger = loggerFactory::createLogger();
    }

    public function index()
    {
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->order_id = $this->session->data['order_id'];
        $data["amount"] = number_format((float)$order_info["total"], 2);
        $data["ambiente"] = $this->get_mode();
        $data["url_second_step"] = $this->config->get('config_url') . "index.php?route=extension/payment/todopago/second_step_todopago";
        $data["formulario"] = $this->config->get('payment_todopago_formulario');
        $data['action'] = $this->config->get('config_url') . "index.php?route=extension/payment/todopago/first_step_todopago";
        $data['todopagoFail'] = $this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $order_info['order_id'];
        $data['validacionJS'] = ($data['ambiente'] === 'test') ? TP_JS_TEST : TP_JS_PROD;
        $this->load->language('extension/payment/todopago');
        $this->load->model('extension/todopago/transaccion');
        $this->load->model('extension/payment/todopago');
        $this->load->model('checkout/order');
        $this->logger->debug("session_data: " . json_encode($this->session->data));
        $this->logger->debug("order_info: " . json_encode($order_info));
        $this->template = 'extension/payment/todopago_form';
        $this->load->model("extension/todopago/bannerbilletera");
        $posicionElegida = $this->model_extension_todopago_bannerbilletera->getUrlBanner();
        $billeteraBanner = new TodopagoBilleterahelper($posicionElegida);
        if ($order_info) {
            $data['order_id'] = $order_info['order_id'];
            $data['completeName'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
            $data['mail'] = $order_info['email'];
            $this->model_extension_payment_todopago->editPaymentMethodOrder($data['order_id']);
        }
        $soyBilletera = strpos(get_class($this), 'billetera');
        $data['tipo'] =  $soyBilletera ? 'billetera' : 'todopago';
        $data['banner'] = $soyBilletera ? $billeteraBanner->getBannerUrl($posicionElegida) : TP_TODOPAGO_BANNER;
        if (key_exists('formulario', $data) && $data["formulario"] != "hibrid")
            $this->template = 'extension/payment/todopago';
        # $this->logger->debug("data cuando entro en formulario hibrido: " . json_encode($data) . "\n" .  $this->template);
        return $this->load->view($this->template, $data);
    }

    //Construye un template para insertar en un body pre-existente
    /*
    protected function errorRedirect($errorMensaje)
    {
        $this->template = 'extension/todopago/todopago_inner_fail';
        $data['errorMessage'] = $errorMensaje;
        $data['next_page'] = $this->url->link('common/home');
        return $this->load->view($this->template, $data);
    }
    */

    public function first_step_todopago()
    {
        $this->load->model('extension/todopago/transaccion');
        if (array_key_exists('order_id', $_POST))
            $this->order_id = $_POST['order_id'];
        elseif (isset($order_id))
            $this->order_id = $order_id;
        else
            $this->logger->warn("first_step_todopago no recibió el ID de la orden");
        $this->openCartPrepareOrder();
        if ($this->model_extension_todopago_transaccion->getStep($this->order_id) == $this->model_extension_todopago_transaccion->getFirstStep()) {
            $this->logger->info("First step");
            $paramsSAR = $this->getPaydata();
            //$payData['todopago_canaldeingresodelpedido'] = $this->config->get('todopago_canaldeingresodelpedido');
            $authorizationHTTP = $this->get_authorizationHTTP();
            $mode = ($this->get_mode() == "test") ? "test" : "prod";
            $this->logger->debug("Authorization: " . json_encode($authorizationHTTP));
            $this->logger->debug('Mode: ' . $mode);
            try {
                $answer = $this->callSAR($authorizationHTTP, $mode, $paramsSAR);
            } catch (Exception $e) {
                $this->openCartRechazo($e);
                $answer = null;
            }
        } else {
            $this->logger->warn("Fallo al iniciar el first step, Ya se encuentra registrado un first step exitoso en la tabla todopago_transaccion");
            $this->response->redirect($this->url->link('common/home'));
            $answer = null;
        }
        echo $answer;
        exit();
    }

    protected function openCartRechazo($mensaje)
    {
        $this->logger->error("Ha surgido un error", $mensaje);
        try {
            $recordHistory = $this->addRecordHistory("TODO PAGO (Exception): " . $mensaje, $this->config->get('payment_todopago_order_status_id_rech'));
        } catch (Exception $e) {
            $this->logger->error($e);
        }
        //Si hubo un error al grabar el registro, recordHistory se convierte en ese error.
        if ($recordHistory)
            $this->logger->debug(print_r($recordHistory));
        $this->response->redirect($this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $mensaje);
    }

    protected function openCartPrepareOrder()
    {
        $this->setLoggerForPayment();
        $this->load->model('extension/todopago/transaccion');
        try {
            $this->model_extension_todopago_transaccion->createRegister($this->order_id);
        } catch (Exception $e) {
            $this->logger->error("Error al guardar en la base de datos\n" . $e);
        }
        $this->load->model('checkout/order');
        //confirma y pasa a pendiente la orden <-- este tiene que ser configurable
        try {
            $this->addRecordHistory(" ", $this->config->get('payment_todopago_order_status_id_pro'));
        } catch (Exception $e) {
            $this->logger->warn("Fallo al actualizar el historial de la compra: \n" . $e);
        }
    }

    protected function getPaydata()
    {
        $this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomer($this->order['customer_id']);

        $this->load->model('extension/payment/todopago');
        $this->model_extension_payment_todopago->setLogger($this->logger);

        $controlFraude = ControlFraudeFactory::getControlfraudeExtractor($this->config->get('payment_todopago_segmentodelcomercio'), $this->order, $customer, $this->model_extension_payment_todopago, $this->logger);

        $controlFraude_data = $controlFraude->getDataCF();

        $paydata_comercial = $this->getOptionsSARComercio();
        $paydata_operation = $this->getOptionsSAROperacion($controlFraude_data);

        return array('comercio' => $paydata_comercial, 'operacion' => $paydata_operation);
    }


    /** GMAPS VALIDACIÓN **/
    //Genera el HASH en base a los datos cargados por el usuario
    protected function SAR_hasher($paramsSAR, $tipoDeCompra)
    {
        if ($tipoDeCompra === 'billing')
            $arrayCompra = array('CSBTSTREET1' => 1, 'CSBTSTATE' => 2, 'CSBTCITY' => 3, 'CSBTCOUNTRY' => 3, 'CSBTPOSTALCODE' => 5);
        elseif ($tipoDeCompra === 'shipping')
            $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
        else {
            $this->logger->error("No se recibió un input válido en el array de SAR_hasher()");
            $arrayCompra = array('CSSTSTREET1' => 1, 'CSSTSTATE' => 2, 'CSSTCITY' => 3, 'CSSTCOUNTRY' => 3, 'CSSTPOSTALCODE' => 5);
        }
        return md5(implode(",", array_intersect_key($paramsSAR, $arrayCompra)));
    }

    //Instancia Google en caso de no encontrar la ubicación a cargar en la tabla
    protected function getGoogleMapsValidator($md5Billing, $md5Shipping)
    {
        if (empty($this->model_extension_todopago_addressbook->findMd5($md5Billing)->row) || empty($this->model_extension_todopago_addressbook->findMd5($md5Shipping)->row)) {
            return new TodoPago\Client\Google();
        } else
            return null;
    }

    //Recupera la información desde la base de datos
    protected function getAddressbookData($operationData, $md5Billing, $md5Shipping) //rellena los datos de la operación con la info almacenada en nuestra agenda
    {
        $arrayBilling = $this->model_extension_todopago_addressbook->getData($md5Billing);
        $arrayShipping = $this->model_extension_todopago_addressbook->getData($md5Shipping);
        if (!empty($arrayBilling->rows)) {
            $operationData['CSBTSTREET1'] = $arrayBilling->row['street'];
            $operationData['CSBTSTATE'] = $arrayBilling->row['state'];
            $operationData['CSBTCITY'] = $arrayBilling->row['city'];
            $operationData['CSBTCOUNTRY'] = $arrayBilling->row['country'];
            $operationData['CSBTPOSTALCODE'] = $arrayBilling->row['postal'];
        }
        if (!empty($arrayShipping->rows)) {
            $operationData['CSSTSTREET1'] = $arrayShipping->row['street'];
            $operationData['CSSTSTATE'] = $arrayShipping->row['state'];
            $operationData['CSSTCITY'] = $arrayShipping->row['city'];
            $operationData['CSSTCOUNTRY'] = $arrayShipping->row['country'];
            $operationData['CSSTPOSTALCODE'] = $arrayShipping->row['postal'];
        }
        return $operationData;
    }

    //Guarda en la base de datos
    protected function setAddressBookData($gResponse, $originalData, $md5Billing, $md5Shipping)
    {
        $originalBilling = array_intersect_key($originalData, $this->requiredDataBuilder('B'));
        $originalShipping = array_intersect_key($originalData, $this->requiredDataBuilder('S'));
        $opBilling = $this->googleResponseValidator($gResponse['billing'], $originalBilling, 'B');
        $opShipping = $this->googleResponseValidator($gResponse['shipping'], $originalShipping, 'S');
        if (!empty($opBilling))
            $this->model_extension_todopago_addressbook->recordAddress($md5Billing, $opBilling['CSBTSTREET1'], $opBilling['CSBTSTATE'], $opBilling['CSBTCITY'], $opBilling['CSBTCOUNTRY'], $opBilling['CSBTPOSTALCODE']);
        if ($md5Billing !== $md5Shipping && !empty($opShipping))
            $this->model_extension_todopago_addressbook->recordAddress($md5Shipping, $opShipping['CSSTSTREET1'], $opShipping['CSSTSTATE'], $opShipping['CSSTCITY'], $opShipping['CSSTCOUNTRY'], $opShipping['CSSTPOSTALCODE']);
    }

    //Comprueba que Google haya devuelo la información correcta
    protected function googleResponseValidator($gFinal, $originalData, $tipoDeCompra)
    {
        $dataDeseada = $this->requiredDataBuilder($tipoDeCompra);
        $comparacion = array_diff_key($dataDeseada, $gFinal);
        if (empty($comparacion))
            return $gFinal;
        else if (array_key_exists('CS' . $tipoDeCompra . 'TPOSTALCODE', $comparacion)) {
            $gFinal = array_merge($gFinal, $originalData);
            return $gFinal;
        } else
            return null;
    }

    //Construye el array a pedir
    protected function requiredDataBuilder($tipoDeCompra)
    {
        $arrayDeseado = array(
            'CS' . $tipoDeCompra . 'TSTREET1' => 1,
            'CS' . $tipoDeCompra . 'TSTATE' => 1,
            'CS' . $tipoDeCompra . 'TCITY' => 1,
            'CS' . $tipoDeCompra . 'TCITY' => 1,
            'CS' . $tipoDeCompra . 'TPOSTALCODE' => 1
        );
        return $arrayDeseado;
    }

    /** END GMAPS **/

    protected function addRecordHistory($statusMessage, $orderStatus)
    {
        $this->load->model('checkout/order');
        try {
            $this->model_checkout_order->addOrderHistory($this->order_id, $orderStatus, "TODO PAGO: " . $statusMessage);
            return false;
        } catch (Exception $e) {
            $error = "Hubo un error al registrar la orden: " . $this->order_id . "\n" . $e;
            $this->logger->warn($error);
            return $error;
        }
    }

    protected function callSAR($authorizationHTTP, $mode, $paramsSAR)
    {
        $this->logger->info("params SAR: " . json_encode($paramsSAR));
        try {
            $this->load->model('extension/todopago/sdktodopago');
        } catch (Exception $e) {
            $this->logger->debug($e);
        }
        try {
            $rtaFirstStepSDK = $this->model_extension_todopago_sdktodopago->sendAuthorizeRequest($authorizationHTTP, $mode, $paramsSAR, $this->logger);
        } catch (Exception $e) {
            $rtaFirstStepSDK = false;
            $this->logger->debug($e);
        }
        if (!$rtaFirstStepSDK) {
            $this->openCartRechazo("ERROR INTERNO");
        } else if ($rtaFirstStepSDK->code === 200) {
            return $this->afterSar($rtaFirstStepSDK->respuesta, $paramsSAR);
        } else {
            $this->openCartRechazo($rtaFirstStepSDK->mensaje);
        }


        return 500;
    }

    protected function openCartCleanCart()
    {
        if ($this->config->get('payment_todopago_cart') == 1) {
            $this->load->language('checkout/cart');
            $this->cart->clear();
            unset($this->session->data['vouchers']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
        }
    }

    protected function afterSar($rtaFirstStep, $paramsSAR)
    {
        /* GMAPS */
        // $md5Billing = null;
        // $md5Shipping = null;
        /*
            if ($this->config->get('payment_todopago_gmaps_validacion')) {
                    $this->load->model('extension/todopago/addressbook');
                    $md5Billing = $this->SAR_hasher($paramsSAR['operacion'], 'billing');
                    $md5Shipping = $this->SAR_hasher($paramsSAR['operacion'], 'shipping');
                    $gMapsValidator = $this->getGoogleMapsValidator($md5Billing, $md5Shipping);
                }
                if (isset($gMapsValidator))
                    $connector->setGoogleClient($gMapsValidator);
                if ($this->config->get('payment_todopago_gmaps_validacion') && !isset($gMapsValidator))
                    $paydata_operation = $this->getAddressbookData($paydata_operation, $md5Billing, $md5Shipping);
        */
        /*        if ($this->config->get('payment_todopago_gmaps_validacion') && isset($gMapsValidator))
                    $this->setAddressBookData($connector->getGoogleClient()->getFinalAddress(), $paramsSAR['operacion'], $md5Billing, $md5Shipping);
        */
        $this->logger->debug($paramsSAR);
        if ($rtaFirstStep["StatusCode"] == -1) {
            $this->logger->info("response SAR: " . json_encode($rtaFirstStep));
            $query = $this->model_extension_todopago_transaccion->recordFirstStep($this->order_id, $paramsSAR, $rtaFirstStep, $rtaFirstStep['RequestKey'], $rtaFirstStep['PublicRequestKey']);
            # $this->logger->debug('query recordFirstStep(): ' . $query);
            #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_pro'), "TODO PAGO: " . $rta_first_step['StatusMessage']);
            $recordFail = $this->addRecordHistory($rtaFirstStep['StatusMessage'], $this->config->get('payment_todopago_order_status_id_pro'));
            if ($recordFail) {
                $this->logger->warn("Error al crear nuevo registro de orden en la base de datos: \n" . $recordFail);
                return 500;
            }
            if ($this->config->get('payment_todopago_formulario') == "hibrid") {
                return json_encode($rtaFirstStep);
            } else {
                header('Location: ' . $rtaFirstStep['URL_Request']);
            }
            //$this->response->redirect($rta_first_step['URL_Request']);

        } else {
            $query = $this->model_extension_todopago_transaccion->recordFirstStep($this->order_id, $paramsSAR, $rtaFirstStep);
            # $this->logger->debug('query recordFirstStep(): ' . $query);
            if ($rtaFirstStep["StatusCode"] >= 98000 && $rtaFirstStep["StatusCode"] < 99000) {
                $this->load->model('extension/payment/todopago');
                $statusMessage = $this->model_extension_payment_todopago->getErrorInfo($rtaFirstStep["StatusCode"]);
                #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_rech'), "TODO PAGO: " . $statusMessage);
                $recordFail = $this->addRecordHistory($statusMessage, $this->config->get('payment_todopago_order_status_id_rech'));
                if ($recordFail) {
                    $this->logger->error("Hubo un error al guardar la orden nueva: " . $recordFail);
                }
                $this->logger->info("Modulo de pago - TodoPago ==> Error de CS --> " . $statusMessage);

                $this->openCartCleanCart();

                if ($this->config->get('payment_todopago_formulario') == "hibrid") {
                    $rtaFirstStep["URL_ErrorPageHybrid"] = $this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $rtaFirstStep['StatusMessage'];
                    return json_encode($rtaFirstStep);
                } else {
                    $this->response->redirect($this->config->get('config_url') . "index.php?route=" . "extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $rtaFirstStep['StatusMessage']);
                }
            } else {
                #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_rech'), "TODO PAGO: " . $rta_first_step['StatusMessage']);
                $this->addRecordHistory($rtaFirstStep['StatusMessage'], $this->config->get('payment_todopago_order_status_id_rech'));
                $this->openCartCleanCart();
                if ($this->config->get('payment_todopago_formulario') == "hibrid") {
                    //En caso de que sea formulario hibrido
                    $rtaFirstStep["URL_ErrorPageHybrid"] = $this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $rtaFirstStep['StatusMessage'];
                    return json_encode($rtaFirstStep);
                } else {
                    $this->response->redirect($this->config->get('config_url') . "index.php?route=" . "extension/payment/todopago/url_error&Order=" . $this->order_id);
                }
            }
        }
    }

    public function second_step_todopago($order = null)
    {
        if (key_exists('Order', $_GET))
            $this->order_id = $_GET['Order'];
        elseif ($order)
            $this->order_id = $order;
        else
            $this->logger->error("Hubo un error al realizar el second step. No hay order ID");
        if (isset($_GET['Answer'])) {
            $answer = $_GET['Answer'];

            $this->load->model('extension/todopago/transaccion');
            $this->setLoggerForPayment();

            if ($this->model_extension_todopago_transaccion->getStep($this->order_id) == $this->model_extension_todopago_transaccion->getSecondStep()) {

                //Starting second Step
                $this->logger->info("second step");

                $authorizationHTTP = $this->get_authorizationHTTP();

                $mode = ($this->get_mode() == "test") ? "test" : "prod";
                $this->load->model('checkout/order');
                $requestKey = $this->model_extension_todopago_transaccion->getRequestKey($this->order_id);
                // $this->logger->debug('Request Key: ' . $requestKey);
                $optionsAnswer = array(
                    'Security' => $this->get_security_code(),
                    'Merchant' => $this->get_id_site(),
                    'RequestKey' => $requestKey,
                    'AnswerKey' => $answer
                );
                $this->logger->info("params GAA: " . json_encode($optionsAnswer));
                try {
                    $this->callGAA($authorizationHTTP, $mode, $optionsAnswer);
                } catch (Exception $e) {
                    #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_rech'), "TODO PAGO (Exception): " . $e);
                    $this->addRecordHistory("TODO PAGO (Exception): " . $e, $this->config->get('payment_todopago_order_status_id_rech'));
                    $this->logger->error("Error en el Second Step", $e);
                    $this->response->redirect($this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $this->order_id);

                }
            } else {
                $this->logger->warn("Fallo al iniciar el second step, Ya se encuentra registrado un second step exitoso en la tabla todopago_transaccion");
                $this->response->redirect($this->url->link('common/home'));
            }
        } else {
            if ($this->config->get('payment_todopago_cart') == 1) {
                $this->load->language('checkout/cart');
                $this->cart->clear();

                unset($this->session->data['vouchers']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['reward']);
            }

            $this->load->model('checkout/order');
            $this->logger->warn('fail: ' . $_GET['ResultMessage']);
            #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_rech'), "TODO PAGO: " . $_GET['ResultMessage']);
            $this->addRecordHistory("TODO PAGO: " . $_GET['ResultMessage'], $this->config->get('payment_todopago_order_status_id_rech'));
            $this->response->redirect($this->config->get('config_url') . "index.php?route=extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $_GET['ResultMessage']);
        }
    }

    protected function callGAA($authorizationHTTP, $mode, $optionsAnswer)
    {
        $connector = new TodoPago\Sdk($authorizationHTTP, $mode);
        $rta_second_step = $connector->getAuthorizeAnswer($optionsAnswer);
        $this->logger->info("response GAA: " . json_encode($rta_second_step));
        $query = $this->model_extension_todopago_transaccion->recordSecondStep($this->order_id, $optionsAnswer, $rta_second_step);
        // $this->logger->debug("query recordSecondStep(): " . $query);

        if (strlen($rta_second_step['Payload']['Answer']["BARCODE"]) > 0) {
            $this->showCoupon($rta_second_step);
        }

        if ($rta_second_step['StatusCode'] == -1) {
            // $this->logger->debug('status code: ' . $rta_second_step['StatusCode']);

            #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_aprov'), "TODO PAGO: " . $rta_second_step['StatusMessage']);
            $this->addRecordHistory('TODO PAGO: ' . $rta_second_step['StatusMessage'], $this->config->get('payment_todopago_order_status_id_aprov'));

            // Cambio por Costo Financiero Total
            if (array_key_exists("AMOUNTBUYER", $rta_second_step['Payload']['Request'])) {
                // $this->logger->debug('Commission ->  AMOUNTBUYER = ' . $rta_second_step['Payload']['Request']['AMOUNTBUYER'] . ' - AMOUNT = ' . $rta_second_step['Payload']['Request']['AMOUNT']);
                $this->model_extension_todopago_transaccion->saveCostoFinancieroTotal($this->order_id, $rta_second_step['Payload']['Request']['AMOUNTBUYER']);
            }

            $this->response->redirect($this->url->link('checkout/success'));
        } else {

            if ($this->config->get('payment_todopago_cart') == 1) {
                $this->load->language('checkout/cart');
                $this->cart->clear();
                unset($this->session->data['vouchers']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['reward']);
            }
            $this->logger->warn('fail: ' . $rta_second_step['StatusCode']);
            #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_rech'), "TODO PAGO: " . $rta_second_step['StatusMessage']);
            $this->addRecordHistory("TODO PAGO: " . $rta_second_step['StatusMessage'], $this->config->get('payment_todopago_order_status_id_rech'));
            $this->response->redirect($this->config->get('config_url') . "index.php?route=" . "extension/payment/todopago/url_error&Order=" . $this->order_id . "&Message=" . $rta_second_step['StatusMessage']);
        }
    }

    protected function showCoupon($rta_second_step)
    {
        $nroop = $this->order_id;
        $venc = $rta_second_step['Payload']['Answer']["COUPONEXPDATE"];
        $total = $rta_second_step['Payload']['Request']['AMOUNT'];
        $code = $rta_second_step['Payload']['Answer']["BARCODE"];
        $tipocode = $rta_second_step['Payload']['Answer']["BARCODETYPE"];
        $empresa = $rta_second_step['Payload']['Answer']["PAYMENTMETHODNAME"];

        #$this->model_checkout_order->addOrderHistory($this->order_id, $this->config->get('payment_todopago_order_status_id_off'), "TODO PAGO: " . $rta_second_step['StatusMessage']);
        $this->addRecordHistory("TODO PAGO: " . $rta_second_step['StatusMessage'], $this->config->get('payment_todopago_order_status_id_off'));
        $this->response->redirect($this->url->link("todopago/todopago/cupon&nroop=$nroop&venc=$venc&total=$total&code=$code&tipocode=$tipocode&empresa=$empresa"));
    }

    public function url_error()
    {
        $data['order_id'] = $_GET['Order'];
        $this->logger->debug($_GET);
        if (key_exists('Message', $_GET) && $_GET['Message'] !== null && $_GET['Message'] !== '') {
            try {
                $recordHistory = $this->addRecordHistory("TODO PAGO (Exception): " . $_GET['Message'], $this->config->get('payment_todopago_order_status_id_rech'));
            } catch (Exception $e) {
                $this->logger->error($e);
            }
            if ($recordHistory) {
                $this->logger->error($recordHistory);
            }
            $this->document->setTitle("Fallo en el Pago");
            $this->template = 'extension/todopago/todopago_fail';
            // define children templates
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['heading_title'] = 'Failed Payment!';

            // Text
            if (array_key_exists("token", $this->session->data)) {
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'ssl')
                );
            } else {
                $data['breadcrumbs'][] = array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/dashboard', '', 'SSL')
                );
            }
            $this->load->language('extension/payment/todopago');
            $data['text_basket'] = $this->language->get('text_basket');
            $data['text_checkout'] = $this->language->get('text_checkout');
            $data['text_failure'] = $this->language->get('text_failure');
            $data['text_message'] = '<p>' . $_GET['Message'] . '</p>';
            $data["next_page"] = $this->url->link('common/home');
            $data['button_continue'] = $this->language->get('button_continue');
            // call the "View" to render the output
            $this->response->setOutput($this->load->view($this->template, $data));
        } else {
            $this->response->redirect($this->url->link('checkout/failure'));
        }
    }

    protected function getOptionsSARComercio()
    {
        $paydata_comercial['URL_OK'] = $this->config->get('config_url') . "index.php?route=" . "extension/payment/todopago/second_step_todopago&Order=" . $this->order_id;
        $paydata_comercial['URL_ERROR'] = $this->config->get('config_url') . 'index.php?route=' . 'extension/payment/todopago/second_step_todopago&Order=' . $this->order_id;
        $paydata_comercial['Merchant'] = $this->get_id_site();
        $paydata_comercial['Security'] = $this->get_security_code();
        $paydata_comercial['EncodingMethod'] = 'XML';
        return $paydata_comercial;
    }

    protected function getOptionsSAROperacion($controlFraude)
    {
        $tipoForm = ($this->config->get('payment_todopago_formulario') == "hibrid" ? '-H' : '-E');
        $this->load->model('checkout/order');
        $this->order = $this->model_checkout_order->getOrder($this->order_id);
        $paydata_operation['MERCHANT'] = $this->get_id_site();
        $paydata_operation['OPERATIONID'] = $this->order_id;
        $paydata_operation['AMOUNT'] = number_format($this->order['total'], 2, ".", "");
        $paydata_operation['CURRENCYCODE'] = "032";
        $paydata_operation['EMAILCLIENTE'] = $this->order['email'];
        $paydata_operation['ECOMMERCENAME'] = 'OPENCART';
        $paydata_operation['ECOMMERCEVERSION'] = VERSION;
        $paydata_operation['PLUGINVERSION'] = TP_VERSION . $tipoForm;
        $var = $this->config->get('payment_todopago_maxinstallments');
        if ($var != null) {
            $paydata_operation['MAXINSTALLMENTS'] = $this->config->get('payment_todopago_maxinstallments');
        }

        $timeout = $this->config->get('payment_todopago_timeout_form');
        if ($timeout) {
            $paydata_operation['TIMEOUT'] = $this->config->get('payment_todopago_timeout_form');
        }
        $paydata_operation = array_merge($paydata_operation, $controlFraude);

        $this->logger->debug("Paydata operación: " . json_encode($paydata_operation));

        return $paydata_operation;
    }

    protected function get_authorizationHTTP()
    {

        if ($this->get_mode() == "test") {
            $http_header = html_entity_decode($this->config->get('payment_todopago_authorizationHTTPtest'));

        } else {
            $http_header = html_entity_decode($this->config->get('payment_todopago_authorizationHTTPproduccion'));
        }

        if (is_null(json_decode($http_header, TRUE))) {
            $http_header = array("Authorization" => $http_header);
        } else {
            $http_header = json_decode($http_header, TRUE);
        }

        return $http_header;
    }

    protected function get_mode()
    {
        return html_entity_decode($this->config->get('payment_todopago_modotestproduccion'));
    }

    protected function get_id_site()
    {
        if ($this->get_mode() == "test") {
            $idSite = html_entity_decode($this->config->get('payment_todopago_idsitetest'));
        } else {
            $idSite = html_entity_decode($this->config->get('payment_todopago_idsiteproduccion'));
        }
        $this->logger->debug("Merchant: " . $idSite);
        return $idSite;
    }

    protected function get_security_code()
    {
        if ($this->get_mode() == "test") {
            return html_entity_decode($this->config->get('payment_todopago_securitytest'));
        } else {
            return html_entity_decode($this->config->get('payment_todopago_securityproduccion'));
        }
    }

    protected function setLoggerForPayment()
    {
        $this->load->model('checkout/order');
        $this->order = $this->model_checkout_order->getOrder($this->order_id);
        $this->logger->debug("ORDER AI DI" . $this->order_id);
        $this->logger->debug("order_info: " . json_encode($this->order));
        $mode = ($this->get_mode() == "test") ? "test" : "prod";
        $this->logger = loggerFactory::createLogger(true, $mode, $this->order['customer_id'], $this->order['order_id']);
    }
}
