<?php

namespace Aenet\NFe\Processes;

use Aenet\NFe\Processes\BaseProcess;
use Aenet\NFe\Controllers\AenetController;
use NFePHP\DA\NFe\Danfe;
use stdClass;

class DanfeProcess extends BaseProcess
{
    /**
     * @var AenetController
     */
    protected $aenet;

    public function __construct(stdClass $cad)
    {
        parent::__construct($cad, 'job_danfe.log');
        $this->aenet = new AenetController();
    }
    
    /**
     * Create pdf from xml
     * @param int $id
     * @param string $xml
     * @return bool
     */
    public function render($id, $xml)
    {
        try {
            $logo =  $this->loadLogo();
            $pdf = '';
            $danfe = new Danfe($xml);
            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('AENET Sistemas - http://www.aenet.com.br');
            $pdf = $danfe->render($logo);
            $astd = [
                'arquivo_nfe_pdf' => base64_encode($pdf),
                'data_danfe' => date('Y-m-d'),
                'data_danfe_h' => date('H:i:s'),
                'nfe_pdf_gerado' => '1'
            ];
            $this->aenet->update($id, $astd);
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


    protected function saveLogo($logopath)
    {
        $image = $this->cad->logo;
        if (empty($image)) {
            //se está em branco não usar LOGO
            return '';
        }
        if (is_file($logopath)) {
            //se já existir usar logo existente
            return $logopath;
        }
        //verifica string do logo
        if ($image !== base64_encode(base64_decode($image))) {
            //imagem invalida
            $this->logger->error("Exception: ["
                . $this->cad->id_empresa
                . "] Imagem do Logo é inválida ou está corrompida.");
            return '';
        }
        $img = gzdecode(base64_decode($image));
        $tmppath = $this->storage."/images/logo_".$this->cad->id_empresa.".img";
        file_put_contents($tmppath, $img);
        $type = exif_imagetype($tmppath);
        unlink($tmppath);
        if ($type !== 2 && $type !== 3) {
            //imagem invalida
            $this->logger->error("Exception: ["
                . $this->cad->id_empresa
                . "] Imagem do Logo é inválida não é JPG, nem PNG.");
            return '';
        }
        $logo = imagecreatefromstring($img);
        imagejpeg($logo, $logopath, 90);
        imagedestroy($logo);
    }
}
