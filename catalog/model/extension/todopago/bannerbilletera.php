<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 22/06/18
 * Time: 15:40
 */

class ModelExtensionTodopagoBannerbilletera extends Model
{
    protected $urlBanner;
    protected $tablaSettings = DB_PREFIX . 'setting';

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->urlBanner = $this->queryUrlBanner();
    }

    protected function queryUrlBanner() {
        $query = $this->db->query("SELECT `value` FROM $this->tablaSettings WHERE `key` = 'payment_todopagobilletera_banner'");
        if($query) {
            return $query->row["value"];
        }
        return 1;
    }

    public function getUrlBanner() {
        return $this->urlBanner;
    }
}