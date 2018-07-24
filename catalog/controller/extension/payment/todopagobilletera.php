<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 15/06/18
 * Time: 16:01
 */

require_once DIR_APPLICATION . 'controller/extension/todopago/vendor/autoload.php';
require_once DIR_APPLICATION . 'controller/extension/todopago/ControlFraude/includes.php';
require_once DIR_SYSTEM . 'library/todopago/Logger/loggerFactory.php';
require_once DIR_SYSTEM . 'library/todopago/todopago_ctes.php';
require_once 'todopago.php';

class ControllerExtensionPaymentTodopagobilletera extends ControllerExtensionPaymentTodopago
{
}