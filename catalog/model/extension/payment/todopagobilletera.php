<?php

require_once 'todopago.php';
require_once DIR_APPLICATION . '../system/library/todopago/todopago_ctes.php';
require_once DIR_APPLICATION . '../system/library/todopago/Billeterahelper.php';

class ModelExtensionPaymentTodopagobilletera extends ModelExtensionPaymentTodopago
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->fillCyberSourceError();
    }

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/todopagobilletera');
        $this->load->model("extension/todopago/bannerbilletera");
        $posicionElegida = $this->model_extension_todopago_bannerbilletera->getUrlBanner();
        $billetera       = new TodopagoBilleterahelper($posicionElegida);
        $banner          = $billetera->getBannerUrl();
        $method_data     = array(
            'code'       => 'todopagobilletera',
            'title'      => '<img src="' . $banner . '" style="height:30px; width:200px" alt="Todo Pago" title="Todo Pago" /></a>',
            'terms'      => '',
            'sort_order' => $this->config->get('payment_todopagobilletera_sort_order')
        );
        return $method_data;
    }

}
