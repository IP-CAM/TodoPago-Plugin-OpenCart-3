<?php

require_once DIR_APPLICATION . '../system/library/todopago/todopago_ctes.php';

class TodopagoBilleterahelper
{
    protected $bannerUrl;
    protected $billetera_banners;
    public function __construct($posicion)
    {
        $this->billetera_banners = array(
            array(
                "img" => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta1.jpg",
                "value" => 1
            ),
            array(
                "img" => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta2.jpg",
                "value" => 2
            ),
            array(
                "img" => "https://todopago.com.ar/sites/todopago.com.ar/files/billetera/pluginstarjeta3.jpg",
                "value" => 3
            )
        );
        $bannerElegido = $posicion;
        $pos = $bannerElegido ? $bannerElegido : 1;
        $this-> bannerUrl =  $this-> billetera_banners[$pos - 1]["img"];
    }

    public function getBannerUrl() {
        return $this->bannerUrl;
    }
}
