<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 04/08/17
 * Time: 12:23
 */

class ModelExtensionTodopagoGetorderstatus extends Model
{
    protected function parseAnswerSAR($serverOutput){
        try {
            $answer = json_decode($serverOutput);
        } catch (Exception $e) {
            $answer = false;
        }
        if (!$answer){
            $answer = false;
        }
        return $answer;
    }

    protected function sliceAnswer($serverOutput){
        return substr($serverOutput, strpos($serverOutput, '{'));
    }

    public function callATodoPago($action, $orderId)
    {
        $this->logger->debug("INGRESO A CALL: " . $action . "\n" . $orderId);
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
        }
        $answer = $this->parseAnswerSAR($server_output);
        if (!$answer){
            $answer = $this->parseAnswerSAR($this->sliceAnswer($server_output));
        }
        if ($answer && is_object($answer))
            return $answer;
        else {
            $this->logger->error("Hay un problema de configuración, revise el log\n" . print_r($server_output, 1));
            return 500;
        }
    }
}