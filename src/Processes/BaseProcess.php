<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Common\Config;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Certificate\CertificationChain;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use stdClass;

class BaseProcess
{
    /**
     * @var stdClass
     */
    protected $cad;
    /**
     * @var string
     */
    protected $config;
    /**
     * @var NFePHP\NFe\Tools
     */
    protected $tools;
    /**
     * @var Monolog
     */
    protected $logger;
    /**
     * @var string
     */
    protected $storage;
    
    public function __construct(stdClass $cad, $pathlog)
    {
        $this->cad = $cad;
        $config = new Config(
            $cad->fantasia,
            $cad->uf,
            $cad->cnpj,
            $cad->tpAmb
        );
        $this->config = "{$config}";
        $this->storage = realpath(__DIR__ .'/../../storage');
        $this->logger = new Logger('Aenet');
        $this->logger->pushHandler(
            new StreamHandler($this->storage . '/' . $pathlog, Logger::WARNING)
        );
        $this->loadTools();
    }
    
    protected function loadTools()
    {
        //carrega o certificado
        $certificate = Certificate::readPfx(
            base64_decode($this->cad->crtpfx),
            $this->cad->crtpass
        );
        $certificate->chainKeys = new CertificationChain(
            $this->cad->crtchain
        );
        //carrega a classe de comunicação
        $this->tools = new Tools($this->config, $certificate);
        $this->tools->model('55');
        $soap = new SoapCurl();
        $soap->disableSecurity(true);
        $soap->setDebugMode(true);
        $this->tools->loadSoapClass($soap);
    }
}
