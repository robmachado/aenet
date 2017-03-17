<?php

namespace Aenet\NFe;

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Certificate\CertificationChain;
use stdClass;
use Aenet\NFe\Models\Status;
use Aenet\NFe\Common\Config;

class StatusController
{
    public $uf;
    public $tpAmb;
    public $status;
    public $error = '';
    
    protected $cad;
    protected $config;

    public function __construct(stdClass $cad)
    {
        $this->cad = $cad;
        $config = new Config(
            'Qualquer coisa',
            $cad->uf,    
            $cad->cnpj,
            $cad->tpAmb
        );
        $this->config = "{$config}";
    }
    
    /**
     * Pull status from SEFAZ
     * @param string $uf
     * @param int $tpAmb
     */
    public function pull($uf = null, $tpAmb = null)
    {
        try {
            //carrega o certificado
            $certificate = Certificate::readPfx(
                base64_decode($this->cad->crtpfx),
                $this->cad->crtpass
            );
            $certificate->chainKeys = new CertificationChain(
                $this->cad->crtchain
            );
            //carrega a classe de comunicação
            $tools = new Tools($this->config, $certificate);
            $tools->model('55');
            $soap = new SoapCurl();
            $soap->setDebugMode(false);
            $tools->loadSoapClass($soap);
            if (!empty($tpAmb)) {
                //corrige o ambiente
                $tools->tpAmb = $tpAmb;
            }    
            if (empty($uf)) {
                $uf = $this->cad->uf;
            }
            //executa a chama SOAP
            $response = $tools->sefazStatus($uf);
            return $reposnse;
        } catch (InvalidArgumentException $e) {
            
        } catch (Exception $e) {
            
        }
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
