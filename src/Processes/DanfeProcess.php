<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\AenetController;
use NFePHP\DA\NFe\Danfe;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use stdClass;

class DanfeProcess  extends BaseProcess
{
    protected $aenet;
    protected $logger;
    protected $storage;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
        $this->aenet = new AenetController();
        $this->storage = realpath(__DIR__ .'/../../storage');
        $this->logger = new Logger('Aenet');
        $this->logger->pushHandler(
            new StreamHandler($this->storage.'/job_danfe.log', Logger::WARNING)
        );
    }
    
    /**
     * Create pdf from xml
     * @param int $id
     * @param string $xml
     * @return bool 
     */
    public function render($id, $xml)
    {
        //imprime o DANFE
        $logopath = $this->storage."/images/logo_".$this->cad->id_empresa.".jpg";
        if (!is_file($logopath)) {
            $logo = base64_decode($this->cad->logo);
            file_put_contents($logopath, $logo);
        }
        try {
            $pdf = '';
            $danfe = new Danfe($xml, 'P', 'A4', $logopath, 'I', '');
            $danfe->montaDANFE();
            $pdf = $danfe->render();
            $astd = [
                'arquivo_nfe_pdf' => base64_encode($pdf),
                'data_danfe' => date('Y-m-d H:i:s'),
                'nfe_pdf_gerado' => '1'
            ];
            $this->aenet->update($id, $astd);
            return true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $error");
        }
        return false;
    }    
}
