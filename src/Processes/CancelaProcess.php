<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\CancelaController;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use stdClass;

class CancelaProcess extends BaseProcess
{
    protected $canc;
    protected $cmpt;
    protected $nfestd;
    
    /**
     * Constructor
     * @param stdClass $cad
     */
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_cancela.log');
        $this->canc = new CancelaController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
    }
        
    
    public function cancela($id, $chave, $xJust, $nProt)
    {
        try {
            //envia solicitação de cancelamento
            $response = $this->tools->sefazCancela($chave, $xJust, $nProt);
            $request = $this->tools->lastRequest;
            $ret = $this->nfestd->toStd($response);
            
            $cStat = $ret->cStat;
            $xMotivo = $ret->xMotivo;
            //se falha
            if ($cStat != 128) {
                $this->logger->error("Erro: $response");
                $astd = [
                    'status' => 8,
                    'motivo' => "$cStat - $xMotivo"
                ];
                $this->canc->update($id, $astd);
                return false;
            }
            $dh = new \DateTime($ret->retEvento->infEvento->dhRegEvento);
            //se não for cStat 101 ou 155 (fora do prazo)
            //deve ser algum erro
            $evStat = $ret->retEvento->infEvento->cStat;
            $status = 1;
            if ($evStat != 101 && $evStat != 155) {
                $status = 8;
                $xml = $response;
            } else {
                //sucesso então protocolar
                $xml = $this->cmpt->toAuthorize($request, $response);
            }
            $astd = [
                'status' => $status,
                'motivo' => $evStat
                    . ' - '
                    . $ret->retEvento->infEvento->xMotivo,
                'xml' => base64_encode($xml),
                'data' => $dh->format('Y-m-d H:i:s')
            ];
            $this->canc->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->canc->update($id, $astd);
            return false;
        }
    }
}
