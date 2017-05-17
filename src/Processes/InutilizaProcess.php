<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\InutilizaController;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use stdClass;

class InutilizaProcess extends BaseProcess
{
    protected $inut;
    protected $cmpt;
    protected $nfestd;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_inutiliza.log');
        $this->inut = new InutilizaController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
    }
    
    public function inutiliza($id, $nSerie, $nIni, $nFin, $xJust, $sequencial)
    {
        try {
            $response = $this->tools->sefazInutiliza($nSerie, $nIni, $nFin, $xJust, $sequencial);
            $request = $this->tools->lastRequest;
            $ret = $this->nfestd->toStd($response);
            
            $cStat = $ret->infInut->cStat;
            $xMotivo = $ret->infInut->xMotivo;
            
            $status = 1;
            if ($cStat != 102) {
                $status = 8;
                $xml = $response;
            } else {
                //sucesso então protocolar
                $xml = $this->cmpt->toAuthorize($request, $response);
            }
            $dh = new \DateTime($ret->infInut->dhRecbto);
            $astd = [
                'status' => $status,
                'motivo' => "$cStat - $xMotivo",
                'xml' => base64_encode($xml),
                'data' => $dh->format('Y-m-d H:i:s')
            ];
            $this->inut->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->inut->update($id, $astd);
            return false;
        }
    }
}
