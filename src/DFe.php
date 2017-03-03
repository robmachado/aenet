<?php

namespace Aenet\NFe;

class DFe
{
    
    public $tools;
    public $ultNSU = 0;
    public $maxNSU = 0;
    public $tpAmb = '2';
    
    public function __construct()
    {
        $this->tools = new ToolsNFe('../config/config.json');
        $this->tools->setModelo('55');
        //caso a versão do PHP não possa identificar automaticamente
        //o protocolo a ser usado durante o handshake. Defina o protocolo.
        //$this->tools->setSSLProtocol('SSLv3');
        
        $this->ambiente = $this->tools->ambiente;
        $this->pathNFe = $this->tools->aConfig['pathNFeFiles'];
        $this->pathRes = $this->tools->aConfig['pathNFeFiles'].DIRECTORY_SEPARATOR.$this->ambiente.DIRECTORY_SEPARATOR.'recebidas'.DIRECTORY_SEPARATOR.'resumo';
        $this->tpAmb = $this->tools->aConfig['tpAmb'];
        $this->nsuFilePath = PATH_ROOT.'base';
        $this->getNSU();
    }
    
    /**
     * Carrega o ultimo nsu recebido para fazer a proxima pesquisa
     */
    public function getLastNSU()
    {
        
    }
    
    /**
     * Salva cada nsu recebido na base de dados
     * para serem utilizados posteriormente em outras buscas e operações
     */
    public function saveNSU()
    {
        
    }
    
    /**
     * Usa o webservice DistDFe da SEFAZ AN para trazer os 
     * documentos destinados ao CNPJ indicado e salvar as NSU retornadas
     * na base de dados
     * @param int $limit
     * @param boolean $bIncludeAnomes
     */
    public function getNFe($limit = 10, $bIncludeAnomes = false)
    {
        if ($this->ultNSU == $this->maxNSU) {
            $this->maxNSU++;
        }
        if ($limit > 100 || $limit == 0) {
            $limit = 10;
        }
        $numNSU = 0;
        $descompactar = true;
        $iCount = 0;
        while ($this->ultNSU < $this->maxNSU) {
            $iCount++;
            if ($iCount > ($limit - 1)) {
                break;
            }
            //limpar a variavel de retorno
            $aResposta = array();
            $this->tools->sefazDistDFe(
                'AN',
                $this->tpAmb,
                $cnpj,
                $this->ultNSU,
                $numNSU,
                $aResposta,
                $descompactar
            );
            //se houve retorno de documentos com cStat = 138 entao prosseguir
            if ($aResposta['cStat'] == 138) {
                //carregar as variaveis de controle com base no retorno da SEFAZ
                $this->ultNSU = (int) $aResposta['ultNSU'];
                $this->maxNSU = (int) $aResposta['maxNSU'];
                $this->putNSU($this->ultNSU, $this->maxNSU);
                $this->zExtractDocs($aResposta['aDoc'], $bIncludeAnomes);
            }
            sleep(5);
        }
    }
    
    /**
     * zSalva
     * Recebe um array com a chave, data e o xml das NFe destinadas
     * e grava na pasta das recebidas/<anomes>
     * 
     * @param array $aDocs
     * @param boolean $bIncludeAnomes
     */
    protected function zSalva($aDocs = array(), $dir = 'recebidas', $bIncludeAnomes = false)
    {
        if (empty($aDocs)) {
            return;
        }
        $path = $this->pathNFe .
            DIRECTORY_SEPARATOR .
            $this->ambiente .
            DIRECTORY_SEPARATOR .
            $dir;
        foreach ($aDocs as $doc) {
            $anomes = $doc['anomes'];
            $chave =  $doc['chave'];
            $xml = $doc['xml'];
            $name = $doc['tipo'];
            $pathnfe = $path;
            if ($bIncludeAnomes) {
                $pathnfe = $path.DIRECTORY_SEPARATOR.$anomes;
            }
            $filename = "$chave$name";
            //echo "Salvando $filename \n";
            FilesFolders::saveFile($pathnfe, $filename, $xml);
        }
    }
    
    /**
     * zExtractNFe
     * Recebe o array com os documentos retornados pelo
     * webservice e caso sejam NFe retorna outro array com 
     * a chave, data e o xml
     * 
     * @param array $docs
     * @return array
     */
    protected function zExtractDocs($docs = array(), $bIncludeAnomes = false)
    {
        $aResp = array();
        //para cada documento retornado
        foreach ($docs as $resp) {
            $schema = substr($resp['schema'], 0, 6);
            switch ($schema) {
                case 'resNFe':
                    $aDocs = self::zTrataResNFe($resp);
                    //mostar as notas resumo e manifestar
                    $this->zSalva($aDocs, 'recebidas/resumo', false);
                    break;
                case 'procNF':
                    $aDocs = self::zTrataProcNFe($resp);
                    $this->zSalva($aDocs, 'recebidas', $bIncludeAnomes);
                    break;
                case 'procEv':
                    $aDocs = self::zTrataProcEvent($resp);
                    //$this->zSalva($aDocs, 'recebidas/resumo', $bIncludeAnomes);
                    break;
            }
        }
        return $aResp;
    }
    
    /**
     * zTrataResNFe
     * Trata os resumos recebidos de NFe que devem ser manifestadas
     * @param array $resp
     * @return array
     */
    private static function zTrataResNFe($resp = array())
    {
        $aResp = array();
        $content = $resp['doc'];
        $dom = new Dom();
        $dom->loadXMLString($content);
        $xmldata = $dom->saveXML();
        $xmldata = str_replace(
            '<?xml version="1.0"?>',
            '<?xml version="1.0" encoding="utf-8"?>',
            $xmldata
        );
        $anomes = date('Ym', DateTime::convertSefazTimeToTimestamp($dom->getNodeValue('dhEmi')));
        $aResp[] = array(
            'tipo' => '-resNFe.xml',
            'chNFe' => $dom->getNodeValue('chNFe'),
            'cnpj' => $dom->getNodeValue('CNPJ'),
            'cpf' => $dom->getNodeValue('CPF'),
            'xNome' => $dom->getNodeValue('xNome'),
            'tpNF' => $dom->getNodeValue('tpNF'),
            'vNF' => $dom->getNodeValue('vNF'),
            'digval' => $dom->getNodeValue('digVal'),
            'nprot' => $dom->getNodeValue('nProt'),
            'cSitNFe' => $dom->getNodeValue('cSitNFe'),
            'dhEmi' => $dom->getNodeValue('dhEmi'),
            'dhRecbto' => $dom->getNodeValue('dhRecbto'),
            'chave' => $dom->getNodeValue('chNFe'),
            'anomes' => $anomes,
            'xml' => $xmldata
        );
        return $aResp;
    }
    
    /**
     * zTrataProcNFe
     * @param array $resp
     * @return array
     */
    private static function zTrataProcNFe($resp = array())
    {
        $content = $resp['doc'];
        $dom = new Dom();
        $dom->loadXMLString($content);
        $chave = $dom->getChave();
        $data = $dom->getNodeValue('dhEmi');
        if ($data == '') {
            $data = $dom->getNodeValue('dEmi');
        }
        $tsdhemi = DateTime::convertSefazTimeToTimestamp($data);
        $anomes = date('Ym', $tsdhemi);
        $xmldata = $dom->saveXML();
        $xmldata = str_replace(
            '<?xml version="1.0"?>',
            '<?xml version="1.0" encoding="utf-8"?>',
            $xmldata
        );
        $aResp[] = array(
            'tipo' => '-nfe.xml',
            'chave' => $chave,
            'anomes' => $anomes,
            'xml' => $xmldata
        );
        return $aResp;
    }
    
    /**
     * zTrataProcEvent
     * @param array $resp
     */
    private static function zTrataProcEvent($resp = array())
    {
        $aResp = array();
        $content = $resp['doc'];
        $dom = new Dom();
        $dom->loadXMLString($content);
        $xmldata = $dom->saveXML();
        $xmldata = str_replace(
            '<?xml version="1.0"?>',
            '<?xml version="1.0" encoding="utf-8"?>',
            $xmldata
        );
        $data = $dom->getNodeValue('dhEvento');
        $tsdhevento = DateTime::convertSefazTimeToTimestamp($data);
        $anomes = date('Ym', $tsdhevento);
        $tpEvento = $dom->getNodeValue('tpEvento');
        $chave = $dom->getNodeValue('chNFe');
        if ($tpEvento == '110111') {
            //confirmado cancelamento, localizar o xml da NFe recebida
            //na pasta anomes
            $path = $this->pathNFe .
                DIRECTORY_SEPARATOR .
                $this->ambiente .
                DIRECTORY_SEPARATOR .
                "recebidas".
                DIRECTORY_SEPARATOR .
                $anomes;
            $pathFile = $path . DIRECTORY_SEPARATOR . $chave . '-nfe.xml';
            self::zCancela($pathFile);
            $aResp[] = array(
                'tipo' => '-cancNFe.xml',
                'chave' => $chave,
                'anomes' => $anomes,
                'xml' => $xmldata
            );
        } elseif ($tpEvento == '110110') {
            //evento Carta de Correção
            $aResp[] = array(
                'tipo' => '-cce.xml',
                'chave' => $chave,
                'anomes' => $anomes,
                'xml' => $xmldata
            );
        }
        return $aResp;
    }
    
    /**
     * manifesta
     * @param string $chNFe
     * @param string $tpEvento
     */
    public function manifesta($chNFe = '', $tpEvento = '210210')
    {
        $aRetorno = array();
        $xJust = '';
        $this->tools->sefazManifesta(
            $chNFe,
            $this->tpAmb,
            $xJust,
            $tpEvento,
            $aRetorno
        );
        $cStat = $aRetorno['evento'][0]['cStat'];
        if ($cStat == 135 || $cStat == 573 || $cStat == 650) {
            $path = $this->pathRes.DIRECTORY_SEPARATOR.$chNFe.'-resNFe.xml';
            if (is_file($path)) {
                unlink($path);
            }
        }
        return $aRetorno;
    }
    
    /**
     * zCancela
     * Edita a NFe recebida de terceiros indicando o cancelamento
     * @param string $pathFile
     */
    private static function zCancela($pathFile)
    {
        if (is_file($pathFile)) {
            //o arquivo foi localizado, então indicar o cancelamento
            //editando o xml da NFe e substituindo o cStat do protocolo por
            //135 ou 101
            $xml = FilesFolders::readFile($pathFile);
            $nfe = new \DOMDocument();
            $nfe->loadXML($xml);
            $infProt = $nfe->getElementsByTagName('infProt')->item(0);
            $infProt->getElementsByTagName('cStat')->item(0)->nodeValue = '101';
            $nfe->save($pathFile);
        }
    }
}

