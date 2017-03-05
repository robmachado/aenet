<?php

namespace Aenet\NFe;

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Certificate\CertificationChain;

class StatusController
{
    /**
     * Pull status from SEFAZ
     * @param string $uf
     */
    public function pull($uf, $tpAmb)
    {
        $configJson = file_get_contents('config.json');
        $content = file_get_contents('bob.pfx');
        //$chain = new CertificationChain(file_get_contents('chain.pem'));
        $certificate = Certificate::readPfx($content, 'fima');
        //$certificate->chainKeys = $chain;
        $tools = new Tools($configJson, $certificate);
        $tools->model('55');
        $soap = new SoapCurl();
        $soap->setDebugMode(false);
        $tools->loadSoapClass($soap);
        $tools->tpAmb = $tpAmb;
        $response = $tools->sefazStatus($uf);
        
    }
    
    /**
     * Save status on table aenet_nfe.sefaz_status
     * @param string $uf
     * @param int $tpAmb
     * @param int $status
     * @param string $error
     */
    public function update($uf, $tpAmb, $status, $error = '')
    {
        
    }
    
}
