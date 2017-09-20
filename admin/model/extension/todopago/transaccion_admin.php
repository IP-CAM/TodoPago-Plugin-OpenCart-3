<?php
require_once DIR_APPLICATION . '../catalog/model/extension/todopago/transaccion.php';

class ModelExtensionTodopagoTransaccionAdmin extends ModelExtensionTodopagoTransaccion
{

    public function createTable()
    {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "todopago_transaccion` (`id` INT NOT NULL AUTO_INCREMENT,`id_orden` INT NULL, `first_step` TIMESTAMP NULL,`params_SAR` TEXT NULL, `response_SAR` TEXT NULL, `second_step` TIMESTAMP NULL, `params_GAA` TEXT NULL, `response_GAA` TEXT NULL, `request_key` TEXT NULL, `public_request_key` TEXT NULL, `answer_key` TEXT NULL, PRIMARY KEY (`id`));");
        } catch (Exception $e) {
            $error = "Hubo un error: " . $e . "en la carga de la tabla todopago_transaccion";
        }
        if (isset($error))
            return $error;
        else
            return 200;
    }

    public function dropTable()
    {
        try {
            $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "todopago_transaccion`;");
        } catch (Exception $e) {
            $error = "Hubo un error: " . $e . "en la desinstalación de la tabla todopago_transaccion";
        }
        if (isset($error))
            return $error;
        else
            return 200;
    }
}
