<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\EventoController;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use stdClass;

class EventoProcess extends BaseProcess
{
    protected $evt;
    protected $cmpt;
    protected $nfestd;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_evento.log');
        $this->evt = new EventoController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
    }
    
    public function send($id, $chave, $xCorrecao, $nSeqEvento = 1)
    {
        try {
            //envia a CCe
            $response = $this->tools->sefazCCe(
                $chave,
                $xCorrecao,
                $nSeqEvento
            );
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
                $this->evt->update($id, $astd);
                return false;
            }
            $dh = new \DateTime($ret->retEvento->infEvento->dhRegEvento);
            //se não for cStat 135 ou 136 (evento não vinculado a NFe)
            //deve ser algum erro
            $evStat = $ret->retEvento->infEvento->cStat;
            $status = 1;
            if ($evStat != 135 && $evStat != 136) {
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
            $this->evt->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $astd = [
                'status' => 9,
                'motivo' => $error
            ];
            $this->logger->error("Exception: $id - $error");
            $this->evt->update($id, $astd);
            return false;
        }
    }
}
