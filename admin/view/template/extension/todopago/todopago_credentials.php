<?php

require_once __DIR__ . '/../../../../../catalog/controller/extension/todopago/vendor/autoload.php';

$mail = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
$pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);


if ((isset($mail) && !empty($mail)) && (isset($pass) && !empty($pass))) {
    $userArray = array(
        "user" => trim($mail),
        "password" => trim($pass)
    );

    $http_header = array();

    //ambiente developer por defecto 
    $mode = "test";
    if ($_POST["ambiente"] == "prod") {
        $mode = "prod";
    }

    try {
        $connector = new \TodoPago\Sdk($http_header, $mode);
        $userInstance = new \TodoPago\Data\User($userArray);
        $rta = $connector->getCredentials($userInstance);
        $security = explode(" ", $rta->getApikey());
        $response = array(
            "codigoResultado" => 0,
            "merchandid" => $rta->getMerchant(),
            "apikey" => $rta->getApikey(),
            "security" => $security[1]
        );
    } catch (\TodoPago\Exception\ResponseException $e) {
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );
    } catch (\TodoPago\Exception\ConnectionException $e) {
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );
    } catch (\TodoPago\Exception\Data\EmptyFieldException $e) {
        $response = array(
            "mensajeResultado" => $e->getMessage()
        );
    }

    echo json_encode($response);

} else {
    $response = array(
        "mensajeResultado" => "Ingrese usuario y contraseña de Todo Pago"
    );

    echo json_encode($response);
}

/*
$ambiente = $_POST["tab"];
$data = array("USUARIO"=> $mail, "CLAVE"=>$pass);

$end_point = ($ambiente == "test") ? "https://developers.todopago.com.ar/api/Credentials":"https://apis.todopago.com.ar/api/Credentials";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $end_point,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
    CURLOPT_SSL_VERIFYHOST=>0,
    CURLOPT_SSL_VERIFYPEER=>0,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($data),
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
*/