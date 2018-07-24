<?php
include_once DIR_APPLICATION . '../system/library/todopago/Logger/loggerFactory.php';
require_once DIR_APPLICATION . '../system/library/todopago/todopago_ctes.php';

class ModelExtensionPaymentTodopago extends Model
{
    protected $settingTable = DB_PREFIX . "setting";


    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->logger = loggerFactory::createLogger();
    }

    public function get_orders()
    {

        $get_orders = $this->db->query("SELECT order_id, date_added ,store_name, firstname, lastname, total  FROM `" . DB_PREFIX . "order` WHERE order_status_id<>0 AND payment_code='todopago';");

        return $get_orders;
    }

    public function injectBilletera()
    {
        $this->load->model('setting/extension');
        try {

            $this->model_setting_extension->install('payment', 'todopagobilletera');
        } catch (Exception $e) {
            $error = "Hubo un  problema $e al insertar en la base de datos";
        }
        if (isset ($error)) {
            return 500;
        } else {
            return 200;
        }
    }

    public function setProvincesCode()
    {
        try {
            $cs_codeColumn = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "zone` LIKE 'cs_code'");
        } catch (Exception $e) {
            $error = "Hubo un problema: " . $e . "al leer la base de datos";
        }
        if (isset($cs_codeColumn) && $cs_codeColumn->num_rows == 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "zone` ADD cs_code CHAR(1);");
        }
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'V' WHERE code = 'AN' OR code = 'TF';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'B' WHERE code = 'BA';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'K' WHERE code = 'CA';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'H' WHERE code = 'CH';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'U' WHERE code = 'CU';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'X' WHERE code = 'CO';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'W' WHERE code = 'CR';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'C' WHERE code = 'DF';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'R' WHERE code = 'ER';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'P' WHERE code = 'FO';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'Y' WHERE code = 'JU';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'L' WHERE code = 'LP';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'F' WHERE code = 'LR';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'M' WHERE code = 'ME';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'N' WHERE code = 'MI';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'Q' WHERE code = 'NE';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'R' WHERE code = 'RN';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'A' WHERE code = 'SA';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'J' WHERE code = 'SJ';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'D' WHERE code = 'SL';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'Z' WHERE code = 'SC';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'S' WHERE code = 'SF';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'G' WHERE code = 'SD';");
        $this->db->query("UPDATE `" . DB_PREFIX . "zone` SET cs_code = 'T' WHERE code = 'TU';");
        if (isset($error)) {
            return $error;
        } else {
            return 200;
        }
    }

    public function unsetProvincesCode()
    {
        try {
            $cs_codeColumn = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "zone` LIKE 'cs_code'");
        } catch (Exception $e) {
            $error = "Hubo un problema: " . $e . "al leer la base de datos";
        }
        if ($cs_codeColumn->num_rows < 1) {
            return 200;
        }
        try {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "zone` DROP COLUMN cs_code;");
        } catch (Exception $e) {
            $error = "No se pudo pudieron borrar los códigos de ciudad. Error: " . $e;
        }
        if (isset($error)) {
            return $error;
        } else {
            return 200;
        }
    }

    public function setPostCodeRequired($required = true)
    {
        try {
            $this->db->query("UPDATE `" . DB_PREFIX . "country` set postcode_required=" . (int)$required . " Where iso_code_2='AR';"); //Hace obligatorio el código postal para Argentina ya que es necesario para que la compra sea procesada. En caso de que $required = false lo setea  como no obligatorio.
        } catch (Exception $e) {
            $error = "No pudo setearse el código postal requerido. Error: " . $e;
        }
        if (isset($error)) {
            return $error;
        } else {
            return 200;
        }
    }
}



