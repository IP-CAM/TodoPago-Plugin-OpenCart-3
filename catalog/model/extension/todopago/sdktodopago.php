<?php
/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 13/03/18
 * Time: 11:28
 */

// DAO DE LA SDK
class ModelExtensionTodopagoSdktodopago
{
    protected $authorizationHTTP;
    protected $mode;
    protected $connector;
    protected $paramsSAR;

    public function sendAuthorizeRequest($authorizationHTTP, $mode, $paramsSAR, $logger)
    {
        $this->setAuthorizationHTTP($authorizationHTTP);
        $this->setMode($mode);
        $this->setParamsSAR($paramsSAR);
        $this->setConnector(new Todopago\Sdk($this->getAuthorizationHTTP(), $this->getMode()));
        $rta = new stdClass();
        try {
            $rta->respuesta = $this->getConnector()->sendAuthorizeRequest($this->getParamsSAR()['comercio'], $this->getParamsSAR()['operacion']);
            $rta->code = 200;
            $rta->mensaje = "OK!";
        } catch (Exception $e) {
            $logger->debug($e);
            $rta->respuesta = "";
            $rta->code = 500;
            $rta->mensaje = "Hubo un problema al realizar el SAR \n" . $e;
        }
        return $rta;
    }

    /**
     * @return mixed
     */
    public function getAuthorizationHTTP()
    {
        return $this->authorizationHTTP;
    }

    /**
     * @param mixed $authorizationHTTP
     */
    public function setAuthorizationHTTP($authorizationHTTP)
    {
        $this->authorizationHTTP = $authorizationHTTP;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return mixed
     */
    public function getParamsSAR()
    {
        return $this->paramsSAR;
    }

    /**
     * @param mixed $paramsSAR
     */
    public function setParamsSAR($paramsSAR)
    {
        $this->paramsSAR = $paramsSAR;
    }

    /**
     * @return mixed
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @param mixed $connector
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
    }

}