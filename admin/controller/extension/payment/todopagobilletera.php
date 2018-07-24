<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 18/06/18
 * Time: 13:02
 */

require_once 'todopago.php';

class ControllerExtensionPaymentTodopagobilletera extends ControllerExtensionPaymentTodopago
{
    public function uninstall() {
        parent::uninstall();
        $this->load->model('setting/extension');
        $this->model_setting_extension->uninstall('payment', 'todopago');
    }
}