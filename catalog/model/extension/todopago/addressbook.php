<?php

/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 03/07/17
 * Time: 11:57
 */
class ModelExtensionTodopagoAddressbook extends Model
{
    public function recordAddress($md5, $street, $state, $city, $country, $postalCode)
    {
        if (empty($this->findMd5($md5)->row)) {
            try {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "todopago_addressbook` (md5_hash,street,state,city,country,postal) VALUES ('{$md5}','{$street}','{$state}','{$city}','{$country}','{$postalCode}');");
            } catch (Exception $e) {
                $this->logger->warn("Error al cargar datos a la agenda de direcciones validadas. " . $e);
            }
        }
    }

    public function findMd5($md5)
    {
        $md5Encontrado = $md5;
        if (isset($md5)) {
            try {
                $md5Encontrado = $this->db->query("SELECT md5_hash FROM `" . DB_PREFIX . "todopago_addressbook` WHERE md5_hash='{$md5}' LIMIT 1;");
            } catch (Exception $e) {
                $this->logger->warn("Error al buscar el hash: " . $md5 . "en la agenda de direcciones. " . $e);
                $md5Encontrado = null;
            }
        }
        return $md5Encontrado;
    }

    public function getData($md5)
    {
        try {
            $data = $this->db->query("SELECT street,state,city,country,postal FROM `" . DB_PREFIX . "todopago_addressbook` WHERE md5_hash='{$md5}';");
        } catch (Exception $e) {
            $this->logger->warn("Error al realizar la query. Error devuelto: " . $e);
            $data = null;
        }
        return $data;
    }
}