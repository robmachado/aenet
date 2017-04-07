<?php

namespace Aenet\NFe\Processes;

/**
 * Atualiza os dados do status de todas as SEFAZ autorizadoras
 * 
 */

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\StatusController;
use NFePHP\NFe\Common\Standardize;
use DateTime;
use stdClass;

class StatusProcess extends BaseProcess
{
    protected $same = [
        'SVRS' => ['AC','AL','AP','DF','ES','PB','RJ','RN','RO','RR','SC','SE','TO'],
        'SVAN' => ['MA','PA','PI']
    ];

    protected $auth = [
        'AC'=>'SVRS',
        'AM'=>'AM',
        'BA'=>'BA',
        'CE'=>'CE',
        'GO'=>'GO',
        'MA'=>'SVAN',
        'MG'=>'MG',
        'MS'=>'MS',
        'MT'=>'MT',
        'PE'=>'PE',
        'PR'=>'PR',
        'RS'=>'RS',
        'SP'=>'SP'
    ];
    
    public function __construct(stdClass $cad) 
    {
        parent::__construct($cad);
    }

    public function updateAll()
    {
        $agora = date('Y-m-d H:i:s');
        $std = [];
        foreach ($this->auth as $uf => $sefaz) {
            $std2 = $this->pull($uf, 2);
            $std1 = $this->pull($uf, 1);
            if ($sefaz == 'SVRS' || $sefaz == 'SVAN') {
                //carregar todos os estados com o mesmo resultado
                foreach($this->same[$sefaz] as $sigla) {
                    $std[$sigla] = new \stdClass();                
                    $std[$sigla]->uf = $sigla;
                    $std[$sigla]->status_1 = 1;
                    $std[$sigla]->error_msg_1 = $std1->xMotivo;
                    $std[$sigla]->updated_at_1 = $agora;
                    $std[$sigla]->status_2 = 1;
                    $std[$sigla]->updated_at_2 = $agora;
                    $std[$sigla]->error_msg_2 = $std2->xMotivo;
                    if ($std1->cStat != 107) {
                        $std[$sigla]->status_1 = 0;
                    }
                    if ($std2->cStat != 107) {
                        $std[$sigla]->status_2 = 0;
                    }
                }
            } else {
                $std[$sefaz] = new \stdClass();                
                $std[$sefaz]->uf = $sefaz;
                $std[$sefaz]->status_1 = 1;
                $std[$sefaz]->updated_at_1 = $agora;
                $std[$sefaz]->error_msg_1 = $std1->xMotivo;
                $std[$sefaz]->status_2 = 1;
                $std[$sefaz]->updated_at_2 = $agora;
                $std[$sefaz]->error_msg_2 = $std2->xMotivo;
                if ($std1->cStat != 107) {
                    $std[$sefaz]->status_1 = 0;
                }
                if ($std2->cStat != 107) {
                    $std[$sefaz]->status_2 = 0;
                }
            }  
        }
        $stCtrl = new StatusController();
        foreach($std as $reg) {
            $stCtrl->updateStatus($reg);  
        }
    }
    
     /**
     * Pull status from SEFAZ
     * @param string $uf
     * @param int $tpAmb
     */
    protected function pull($uf, $tpAmb)
    {
        try {
            //executa a chama SOAP
            $response = $this->tools->sefazStatus($uf, $tpAmb);
            //converte a resposta de SOAP para stdClass
            $stClass = new Standardize();
            return $stClass->toStd($response);
        } catch (\NFePHP\Common\Exception\SoapException $e) {
            $error = $e->getmessage();
        } catch (\InvalidArgumentException $e) {
            $error = $e->getmessage();
        } catch (\Exception $e) {
            $error = $e->getmessage();
        }
        $std = new stdClass();
        $std->cStat = 0;
        $std->xMotivo = $error;
        return $std;
    }
}
