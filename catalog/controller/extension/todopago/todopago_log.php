<?php
include_once DIR_SYSTEM.'library/log.php';
include_once DIR_SYSTEM.'library/db.php';

class TodoPagoLog extends Log{
    private $orderId;
    private $customerId;

    function __construct($orderId, $customerId){
        $config = new Config();
        $this->file = $config->get('config_error_filename');
        $this->orderId = $orderId;
        $this->customerId = $customerId;

        //TODO PAGO VERSION:
        $this->db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $actualVersionQuery = $this->db->query("SELECT value FROM `".DB_PREFIX."setting` WHERE `group` = 'todopago' AND `key` = 'todopago_version'");
        $actualVersion = ($actualVersionQuery->num_rows == 0)? "0." : $actualVersionQuery->row['value'];
        if($actualVersion == "0."){
            $todopagoclavecolumn = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."order` LIKE 'todopagoclave'"); //Esta consulta sólo es válida para MySQL 5.0.1+
        //En caso de haber desinstaldo el plugin al instalarlo de nuevo la columna todopagoclave ya se econtraría creada, así que verificamos que no exista antes de crearla

            $actualVersion .= ($todopagoclavecolumn->num_rows != 1)? "0.0" : "9.0";
        }
        $this->tp_version = $actualVersion;


    }

    function writeTP($action, $params = false){
        $logMessage = "TPV ".$this->tp_version." - ".phpversion()." - orden ".$this->orderId.": ".$action;
        $logMessage .= $params? " - parametros: ".json_encode($params):'';
        $this->write($logMessage);
    }
}
