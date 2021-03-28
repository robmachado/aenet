<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Common\Config;
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

    public function __construct(stdClass $cad, $pathlog, $init = true)
    {
        $this->cad = $cad;
        if (strpos($pathlog, 'nfe') != false) {
            $type = 'nfe';
            $config = new Config(
                $cad->fantasia,
                $cad->uf,
                $cad->cnpj,
                $cad->tpAmb,
                'PL_009_V4',
                isset($cad->layout) ? $cad->layout : '4.00'
            );
        } else {
            $type = 'cte';
            $config = new Config(
                $cad->fantasia,
                $cad->uf,
                $cad->cnpj,
                $cad->tpAmb,
                'PL_CTe_300a',
                '3.00'
            );
        }
        $this->config = "{$config}";
        $this->storage = realpath(__DIR__ . '/../../storage');
        $this->logger = new Logger('Aenet');
        $this->logger->pushHandler(
            new StreamHandler($this->storage . '/' . $pathlog, Logger::WARNING)
        );
        if ($init) {
            $this->loadTools($type);
        }
    }

    protected function loadTools($type)
    {
        //carrega o certificado
        $certificate = Certificate::readPfx(
            base64_decode($this->cad->crtpfx),
            $this->cad->crtpass
        );
        $certificate->chainKeys = new CertificationChain(
            $this->cad->crtchain
        );
        if ($type == 'nfe') {
            //carrega a classe de comunicação
            $this->tools = new \NFePHP\NFe\Tools($this->config, $certificate);
            $this->tools->model('55');
            //$this->tools->soap->disableSecurity(true);
            //$this->tools->soap->setDebugMode(false);
        } else {
            //carrega a classe de comunicação
            $this->tools = new \NFePHP\CTe\Tools($this->config, $certificate);
            $this->tools->model('57');
            //$this->tools->soap->disableSecurity(true);
            //$this->tools->soap->setDebugMode(false);
        }    
    }
}
