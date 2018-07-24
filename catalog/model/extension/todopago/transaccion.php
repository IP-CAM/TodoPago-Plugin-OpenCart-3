<?php

class ModelExtensionTodopagoTransaccion extends Model
{

    const NEW_ORDER = 0;
    const FIRST_STEP = 1;
    const SECOND_STEP = 2;
    const TRANSACTION_FINISHED = 3;

    private function getTransaction($orderId)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "todopago_transaccion WHERE id_orden = " . $orderId);

        return $query->row;
    }

    private function getField($orderId, $fieldName)
    {
        $transaction = $this->getTransaction($orderId);

        return $transaction[$fieldName];
    }

    public function getStep($orderId)
    {
        $transaction = $this->getTransaction($orderId);
        if ($transaction == null) {
            $step = self::NEW_ORDER;
        } elseif ($transaction['first_step'] == null) {
            $step = self::FIRST_STEP;
        } elseif ($transaction['second_step'] == null) {
            $step = self::SECOND_STEP;
        } else {
            $step = self::TRANSACTION_FINISHED;
        }

        return $step;
    }

    public function getRequestKey($orderId)
    {
        return $this->getField($orderId, 'request_key');
    }

    public function createRegister($orderId)
    {
        if ($this->getStep($orderId) == self::NEW_ORDER) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "todopago_transaccion (id_orden) VALUES (" . $orderId . ")");

            return 1;
        } else {
            return 0;
        }
    }

    public function recordFirstStep($orderId, $paramsSAR, $responseSAR)
    {
        $datetime = new DateTime('NOW');
        if ($this->getStep($orderId) == self::FIRST_STEP) {
            $requestKey       = isset($responseSAR['RequestKey']) ? $responseSAR['RequestKey'] : null;
            $publicRequestKey = isset($responseSAR['PublicRequestKey']) ? $responseSAR['PublicRequestKey'] : null;
            $query            = "UPDATE " . DB_PREFIX . "todopago_transaccion SET first_step = '" . $datetime->format("Y-m-d H:i:s") . "', params_SAR = '" . $this->db->escape(json_encode($paramsSAR)) . "', response_SAR = '" . $this->db->escape(json_encode($responseSAR)) . "', request_key = '" . $requestKey . "', public_request_key = '" . $publicRequestKey . "' WHERE id_orden = " . $orderId;
            $this->db->query($query);

            return $query;
        } else {
            return 0;
        }
    }

    public function recordSecondStep($orderId, $paramsGAA, $responseGAA)
    {
        $datetime = new DateTime('NOW');
        if ($this->getStep($orderId) == self::SECOND_STEP) {
            $answerKey = isset($paramsGAA['AnswerKey']) ? $paramsGAA['AnswerKey'] : null;
            $query     = "UPDATE " . DB_PREFIX . "todopago_transaccion SET second_step = '" . $datetime->format("Y-m-d H:i:s") . "', params_GAA = '" . $this->db->escape(json_encode($paramsGAA)) . "', response_GAA = '" . $this->db->escape(json_encode($responseGAA)) . "', answer_key = '" . $answerKey . "' WHERE id_orden = " . $orderId;
            $this->db->query($query);

            return $query;
        } else {
            return 0;
        }
    }

    public function saveCostoFinancieroTotal($orderId, $costoTotal)
    {
        try {
            $query         = $this->db->query("SELECT total FROM " . DB_PREFIX . "order WHERE order_id = " . $orderId);
            $totalAnterior = $query->row["total"];

            $this->db->query("UPDATE " . DB_PREFIX . "order SET commission = " . $costoTotal . " - total, total = " . $costoTotal . " WHERE order_id = " . $orderId);

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_total (order_id, code, title, value, sort_order) VALUES (" . $orderId . ", 'commission', 'Otros cargos', " . ($costoTotal - $totalAnterior) . ", 2)");

            $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = " . $costoTotal . " WHERE order_id = " . $orderId . " AND code = 'total' AND title = 'Total'");

        } catch (Exception $e) {
            $this->logger->info(json_encode($e));
        }

        return 0;
    }

    //No puedo acceder a las constantes, so...
    public function getNewOrder()
    {
        return self::NEW_ORDER;
    }

    public function getFirstStep()
    {
        return self::FIRST_STEP;
    }

    public function getSecondStep()
    {
        return self::SECOND_STEP;
    }

    public function getTransactionFinished()
    {
        return self::TRANSACTION_FINISHED;
    }
}
