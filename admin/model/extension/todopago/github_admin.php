<?php

/**
 * Created by PhpStorm.
 * User: maximiliano
 * Date: 10/11/17
 * Time: 15:26
 */
class ModelExtensionTodopagoGithubAdmin extends Model
{
    const API = "https://api.github.com/repos/TodoPago/Plugin-OpenCart3/releases/latest";
    const TOKEN = "token 21600a0757d4b32418c54e3833dd9d47f78186b4";

    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function getReleases()
    {
        $header = array(
            'Content-Type: application/json',
            'Authorization: ' . self::TOKEN
        );
        if (function_exists('curl_version'))
            $ch = curl_init();
        else {
            return 501;
        }
        if (isset($ch)) {
            curl_setopt($ch, CURLOPT_URL, self::API);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch,CURLOPT_FAILONERROR, true);
            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'OpenCart3');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $server_output = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            if (empty($error))
                return $server_output;
            else
                return $error;
        } else {
            return 500;
        }
    }
}