<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\ManifestaController;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;
use stdClass;
use Aenet\NFe\Models\NFe;
use Aenet\NFe\Models\Cadastro;
use Aenet\NFe\Controllers\SmtpController;


class ManifestaProcess extends BaseProcess
{
    protected $evt;
    protected $cmpt;
    protected $nfestd;
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_evento.log');
        $this->man = new ManifestaController();
        $this->cmpt = new Complements();
        $this->nfestd = new Standardize();
    }
    
    public function send($id, $chave, $tipo, $justificativa = null)
    {
        try {
            //envia a manifestação
            $response = $this->tools->sefazManifesta(
                $chave,
                $tipo,
                $justificativa,
                1
            );
            $request = $this->tools->lastRequest;
            $ret = $this->nfestd->toStd($response);
            $cStat = $ret->cStat;
            $xMotivo = $ret->xMotivo;         
            //se falha
            if ($cStat != 128) {
                $this->logger->error("Erro: $response");
                $astd = [
                    'manifestar' => 8,
                    'resultado' => "$cStat - $xMotivo",
                    'evento' => base64_encode($response)
                ];
                $this->man->update($id, $astd);
                return false;
            }
            $evStat = $ret->retEvento->infEvento->cStat;
            $astd = [
                'manifestar' => 8,
                'resultado' => "$cStat - $xMotivo",
                'evento' => base64_encode($response)
            ];
            if (in_array($evStat, [135, 136])) {
                $xml = $this->cmpt->toAuthorize($request, $response);
                $astd = [
                    'manifestar' => 0,
                    'resultado' => $ret->retEvento->infEvento->cStat . '-' . $ret->retEvento->infEvento->xMotivo,
                    'evento' => base64_encode($xml)
                ];
            } elseif ($evStat == 573) {
                $astd = [
                    'manifestar' => 0,
                    'resultado' => $ret->retEvento->infEvento->cStat . '-' . $ret->retEvento->infEvento->xMotivo,
                    'evento' => base64_encode($response)
                ];
            }
            $this->man->update($id, $astd);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception Manifesta: $id - $error");
            return false;
        }
    }
}
