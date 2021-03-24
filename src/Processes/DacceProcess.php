<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\EventoController;
use NFePHP\DA\NFe\Dacce;
use stdClass;

class DacceProcess extends BaseProcess
{
    /**
     * @var EventoController
     */
    protected $evento;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_dacce.log', false);
        $this->evento = new EventoController();
    }
    
    /**
     * Create pdf from xml
     * @param int $id
     * @param string $xml
     * @param array $aEmit
     * @return bool
     */
    public function render($id, $xml, $aEmit)
    {
        try {
            $logo =  $this->loadLogo();
            $pdf = '';
            $daevento = new Daevento($xml, $aEmit);
            $daevento->creditsIntegratorFooter('AENET Sistemas - http://www.aenet.com.br');
            $pdf = $daevento->render($logo);
            $astd = [
                'arquivo_evento_pdf' => base64_encode($pdf),
                'pdf_gerado' => '1'
            ];
            $this->evento->update($id, $astd);
            return true;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->logger->error("Exception: $id - $error");
        }
        return false;
    }
    
    /**
     * Carrega o logo da base de dados em uma string para inclusão
     * direta no PDF
     * @return string
     */
    protected function loadLogo()
    {
        $image = $this->cad->logo;
        if (empty($image)) {
            return '';
        }
        //verifica string do logo
        if ($image !== base64_encode(base64_decode($image))) {
            //imagem invalida corrompida
            $this->logger->error("Exception: ["
                . $this->cad->id_empresa
                . "] Imagem do Logo é inválida ou está corrompida.");
            return '';
        }
        $img = gzdecode(base64_decode($image));
        $ids = [
            "png" => [8, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
            "jpg" => [3, "\xFF\xD8\xFF"]
        ];
        if (substr($img, 0, 3) !== $ids['jpg'][1] //não é jpg
            && substr($img, 0, 8) !== $ids['png'][1] //não é png
        ) {
            //imagem invalida
            $this->logger->error("Exception: ["
                . $this->cad->id_empresa
                . "] Imagem do Logo é inválida! Deve ser JPG ou PNG.");
            return '';
        }
        return 'data://text/plain;base64,'.base64_encode($img);
    }
}
