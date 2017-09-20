<?php

class ControlFraude_Retail extends ControlFraude{

    protected function completeCFVertical(){
        $payDataOperacion = array();
        $this->logger->debug("CSSTCITY - Ciudad de env&iacute;o de la orden");
        $payDataOperacion ['CSSTCITY'] = $this->getField(empty($this->order['shipping_city'])?$this->order['payment_city']:$this->order['shipping_city']);

        $this->logger->debug("CSSTCOUNTRY Pa&iacute;s de env&iacute;o de la orden");
        $payDataOperacion ['CSSTCOUNTRY'] = $this->getField(empty($this->order['shipping_iso_code_2'])?$this->order['payment_iso_code_2']:$this->order['shipping_iso_code_2']);

        $this->logger->debug("CSSTEMAIL Mail del destinatario");
        $payDataOperacion ['CSSTEMAIL'] = $this->getField($this->order['email']);

        $this->logger->debug("CSSTFIRSTNAME Nombre del destinatario");
        $payDataOperacion ['CSSTFIRSTNAME'] = $this->getField(empty($this->order['shipping_firstname'])?$this->order['payment_firstname']:$this->order['shipping_firstname']);

        $this->logger->debug("CSSTLASTNAME Apellido del destinatario");
        $payDataOperacion ['CSSTLASTNAME'] = $this->getField(empty($this->order['shipping_lastname'])?$this->order['payment_lastname']:$this->order['shipping_lastname']);

        $this->logger->debug("CSSTPHONENUMBER N&uacute;mero de tel&eacute;fono del destinatario");
        $payDataOperacion ['CSSTPHONENUMBER'] = phone::clean($this->getField($this->order['telephone']));

        $this->logger->debug("CSSTPOSTALCODE C&oacute;digo postal del domicilio de env&iacute;o");
        $payDataOperacion ['CSSTPOSTALCODE'] = $this->getField(empty($this->order['shipping_postcode'])?$this->order['payment_postcode']:$this->order['shipping_postcode']);

        $this->logger->debug("CSSTSTATE Provincia de envacute;o");
        $stateCode = $this->getField(empty($this->order['shipping_zone_cs_code'])?$this->order['payment_zone_cs_code']:$this->order['shipping_zone_cs_code']);

        if (empty($stateCode)) {
            $stateCode = $this->getField(empty($this->order['shipping_zone_code'])?$this->order['payment_zone_code']:$this->order['shipping_zone_code']);
        }

        $payDataOperacion ['CSSTSTATE'] = (!empty($stateCode)) ? $stateCode[0] : 'C';

        $this->logger->debug("CSSTSTREET1 Domicilio de env&iacute;o");
        $payDataOperacion ['CSSTSTREET1'] = $this->getField(empty($this->order['shipping_address_1'])?$this->order['payment_address_1']:$this->order['shipping_address_1']);

        //$paydata_operation['CSSSTREET2'] = $this->getField($this->order['shipping_city']);

        $this->logger->debug("CSMDD12 Shipping DeadLine (Num Dias)");
        $paydata_operation ['CSMDD12'] = $this->model->getDeadLine();

        $this->logger->debug("CSMDD13 M&eacute;todo de Despacho");
        $payDataOperacion ['CSMDD13'] = $this->getField($this->order['shipping_method']);

        $this->logger->debug("CSMDD14 Customer requires Tax Bill ? (S/N) No");
                //$payData ['CSMDD14'] = "";

        $this->logger->debug("CSMDD15 Customer Loyality Number No");
                //$payData ['CSMDD15'] = "";
        $this->logger->debug("CSMDD16 Promotional / Coupon Code");
        $payDataOperacion ['CSMDD16'] = $this->getField($this->order['coupon_code']);
        $payDataOperacion = array_merge($this->getMultipleProductsInfo(), $payDataOperacion);
        return $payDataOperacion;
    }
}
