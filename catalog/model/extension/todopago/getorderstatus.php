<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 04/08/17
 * Time: 12:23
 */

class ModelExtensionTodopagoGetorderstatus extends Model
{
    public function callATodoPago($action, $orderId)
    {
        $this->logger->debug(function_exists('curl_version'));
        if (function_exists('curl_version'))
            $ch = curl_init();
        else {
            return 501;
        }
        if (isset($ch)) {
            curl_setopt($ch, CURLOPT_URL, $action);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "order_id=$orderId");
            curl_setopt($ch, CURLOPT_HEADER, false);
            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $answer = json_decode($server_output);
        } else {
            return 501;
        }
        if (is_object($answer))
            return $answer;
        else {
            $this->logger->error("Hay un problema de configuración, revise el log");
            return 500;
        }
    }
}