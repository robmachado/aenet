<?php
error_reporting(0);
ini_set('display_errors', 'Off');
require_once '/var/www/aenet/bootstrap.php';

/**
 * Service para validação de certificados
 **/

use NFePHP\Common\Certificate;

try {
    if (isset($_POST['cert'], $_POST['pass'], $_POST['cnpj'])) {
        if (!empty($_POST['cert']) 
            && !empty($_POST['pass']) 
            && !empty($_POST['cnpj'])
        ) {
            $cert = $_POST['cert'];
            $pass = $_POST['pass'];
            $cnpj = $_POST['cnpj'];

            $certificate = Certificate::readPfx(base64_decode($cert), $pass);

            $cnpj_cert = $certificate->getCNPJ();
            if ($cnpj_cert != $cnpj && substr($cnpj_cert, 0, 6) != substr($cnpj, 0, 6)) {
                echo '0|CNPJ (' . $cnpj . ') INFORMADO NAO E O MESMO DO CERTIFICADO(' . $certificate->getCNPJ() . ')';
            } elseif ($certificate->isExpired() === true) {
                echo '0|CERTIFICADO EXPIRADO EM: ' . $certificate->getValidTo()->format('d/m/Y H:m:s');
            } else {
                echo '1|CERTIFICADO VALIDO|' . $certificate->getValidTo()->format('Y-m-d');
            }
        } else {
            echo '0|PARAMETROS INVALIDOS';
        }
    } else {
        echo '0|PARAMETROS INVALIDOS';
    }
} catch (Exception $e) {
    echo '0|' . $e->getMessage();
}
