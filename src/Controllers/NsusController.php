<?php

namespace Aenet\NFe\Controllers;

use stdClass;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Response;
use Aenet\NFe\Controllers\BaseController;
use Aenet\NFe\Models\Nsu;
use Aenet\NFe\Models\Event;
use Aenet\NFe\Models\NFe;

class NsusController extends BaseController
{
    
    public function __construct(stdClass $cad)
    {
        parent::__construct($cad);
    }

    public function pull($limit = 10)
    {
        $ultNSU = $this->getLastNSU();
        $maxNSU = $ultNSU;
        if ($limit > 100 || $limit == 0 || empty($limit)) {
            $limit = 10;
        }
        $iCount = 0;
        //executa a busca de DFe em loop
        while ($ultNSU <= $maxNSU) {
            $iCount++;
            if ($iCount >= ($limit)) {
                break;
            }
            $resp = $this->tools->sefazDistDFe(
                $ultNSU
            );
            //extrair e salvar os retornos
            $dom = new \DOMDocument();
            $dom->loadXML($resp);
            $node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
            $tpAmb = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
            $verAplic = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $cStat = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
            $xMotivo = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $dhResp = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
            $ultNSU = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
            $maxNSU = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
            $lote = $node->getElementsByTagName('loteDistDFeInt')->item(0); 
            $docs = $lote->getElementsByTagName('docZip');
            $d = [];
            foreach($docs as $doc) {
                $nodom = null;
                $emit = null;
                $infNFe = null;
                $nf = null;
                

                $numnsu = $doc->getAttribute('NSU');
                $schema= $doc->getAttribute('schema');
                $content = gzdecode(base64_decode($doc->nodeValue));
                $nodom = new \DOMDocument();
                $nodom->loadXML($content);

                
                if (substr($schema, 0, 6) == 'resNFe') {
                    $dt = new \DateTime($nodom->getElementsByTagName('dhEmi')->item(0)->nodeValue);
                    $nsu = new Nsu();
                    $nsu->id_empresa = $this->cad->id_empresa;
                    $nsu->nsu = $numnsu;
                    $nsu->content = $content;
                    $nsu->tipo = 'resumo';
                    $nsu->manifestar = 0;
                    $nsu->cnpj = $nodom->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    $nsu->chNFe = $nodom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                    $nsu->xNome = $nodom->getElementsByTagName('xNome')->item(0)->nodeValue;
                    $nsu->dhEmi = $dt->format('Y-m-d H:i:s');
                    $nsu->nProt = $nodom->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $nsu->save();
                } elseif (substr($schema, 0, 7) == 'procNFe') {
                    $infNFe = $nodom->getElementsByTagName('infNFe')->item(0);
                    $emit = $nodom->getElementsByTagName('emit')->item(0);
                    $nf = new NFe();
                    $nf->id_empresa = $this->cad->id_empresa;
                    $nf->nsu = $numnsu;
                    $nf->content = $content;
                    $nf->cnpj = $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    $nf->chNFe = substr($infNFe->getAttibute('Id'), 3, 44);
                    $nf->save();
                } elseif (substr($schema, 0, 10) == 'procEvento' || substr($schema, 0, 9) == 'resEvento') {
                    /*
                     * <procEventoNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="1.00">
                     * <evento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">
                     * <infEvento Id="ID2102003516125871652300011955000000044010188543804101">
                     * <cOrgao>91</cOrgao>
                     * <tpAmb>1</tpAmb>
                     * <CNPJ>89850341000160</CNPJ>
                     * <chNFe>35161258716523000119550000000440101885438041</chNFe>
                     * <dhEvento>2017-01-03T15:33:05-02:00</dhEvento>
                     * <tpEvento>210200</tpEvento>
                     * <nSeqEvento>1</nSeqEvento>
                     * <verEvento>1.00</verEvento>
                     * <detEvento versao="1.00">
                     * <descEvento>Confirmacao da Operacao</descEvento>
                     * </detEvento>
                     * </infEvento>
                     * <Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
                     * <SignedInfo>
                     * <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
                     * <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1" />
                     * <Reference URI="#ID2102003516125871652300011955000000044010188543804101">
                     * <Transforms><Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
                     * <Transform Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315" />
                     * </Transforms><DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" />
                     * <DigestValue>W28RqKRXSpnGkCyp4N8rPJocTVE=</DigestValue>
                     * </Reference></SignedInfo>
                     * <SignatureValue>HdyxmpJ76bc/bbAl8MHFUvU4LlPuP5sX5tGsQq3tgtDEH4CcH6G2Lxpc9RQodZNraFsOYrBs7AiVMlM+6zaNEtXbj80Mzg6WvDUpw6IP7CIEdymkeHhUECacSzmjkiWK2XDQ1gsEE6v4AnwYmLlIohBL5UZq93J6LDvvKFVHTNlW/1I7J7ZQRjdpAZ91NEd3NAyqh0uHidqS3zDmeezSYe+/tH3C0Dn4Kure+HJGguH/mRs398SSofj005c4c6TUa4SfGI6ltHO39qqkjetTaDahviOeKW62wiUVOtfDco8HFrceCbuCjXoTWgVokbKgKkvTEzGYJPB+yGKsfr5lrQ==</SignatureValue><KeyInfo><X509Data><X509Certificate>MIIIRTCCBi2gAwIBAgIQXbpMKT4uhbjPb/biOacjXDANBgkqhkiG9w0BAQsFADB0MQswCQYDVQQGEwJCUjETMBEGA1UEChMKSUNQLUJyYXNpbDEtMCsGA1UECxMkQ2VydGlzaWduIENlcnRpZmljYWRvcmEgRGlnaXRhbCBTLkEuMSEwHwYDVQQDExhBQyBDZXJ0aXNpZ24gTXVsdGlwbGEgRzUwHhcNMTYwNjI4MDAwMDAwWhcNMTcwNjI3MjM1OTU5WjCBxjELMAkGA1UEBhMCQlIxEzARBgNVBAoUCklDUC1CcmFzaWwxJDAiBgNVBAsUG0F1dGVudGljYWRvIHBvciBBUiBESUdJQ0VSVDEbMBkGA1UECxQSQXNzaW5hdHVyYSBUaXBvIEExMRYwFAYDVQQLFA1JRCAtIDEwNTkzODUzMRUwEwYDVQQDEwxHUkVOREVORSBTIEExMDAuBgkqhkiG9w0BCQEWIWpvc2lhbmUuYmlhbmNoaW5pQGdyZW5kZW5lLmNvbS5icjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALJsJ7pxh8n49EFmaCePxlt1/bk90kINznPpFeY4M4ZIojIYAjCBkVxjBQ8E/p1n5o2aYwSGdLYjhrYNKhjtBf0mW9oRpm0y1WrPdpzgBQHi3Hx9JPeCMyXM0u+G3U4fSHZhD3LD/SqFtbAM6Na2KE48gbyPMdQMtoYgYr1Lf8lrwaF62Jhv0byOnn0djfMq+Yjep+ZBtMQRzrumOzf9erIZXSQaV/ut4OT/B7oBDLUUAdAfIuk2teFLhAO7xGF6brGNxRe/tSMduIA79119PGY+PdKtlTESLsM1JlzQ+heY5oj+hXXI1ZYUM3o/SrnmV4DG5XwyKkc1FnT5HojkY40CAwEAAaOCA34wggN6MIHEBgNVHREEgbwwgbmgPQYFYEwBAwSgNAQyMTQwMjE5NTMxNDg0MTE0Mjk5MTAwMDAwMDAwMDAwMDAwMDAyMDMxMDk0NDQxU1NQUlOgIQYFYEwBAwKgGAQWR0VMU09OIExVSVMgUk9TVElST0xMQaAZBgVgTAEDA6AQBA44OTg1MDM0MTAwMDE2MKAXBgVgTAEDB6AOBAwwMDAwMDAwMDAwMDCBIWpvc2lhbmUuYmlhbmNoaW5pQGdyZW5kZW5lLmNvbS5icjAJBgNVHRMEAjAAMB8GA1UdIwQYMBaAFJ1Qz73/JMqvsTPrF+JCeo5pKo5TMA4GA1UdDwEB/wQEAwIF4DCBiQYDVR0gBIGBMH8wfQYGYEwBAgELMHMwcQYIKwYBBQUHAgEWZWh0dHA6Ly9pY3AtYnJhc2lsLmNlcnRpc2lnbi5jb20uYnIvcmVwb3NpdG9yaW8vZHBjL0FDX0NlcnRpc2lnbl9NdWx0aXBsYS9EUENfQUNfQ2VydGlTaWduTXVsdGlwbGEucGRmMIIBJQYDVR0fBIIBHDCCARgwXKBaoFiGVmh0dHA6Ly9pY3AtYnJhc2lsLmNlcnRpc2lnbi5jb20uYnIvcmVwb3NpdG9yaW8vbGNyL0FDQ2VydGlzaWduTXVsdGlwbGFHNS9MYXRlc3RDUkwuY3JsMFugWaBXhlVodHRwOi8vaWNwLWJyYXNpbC5vdXRyYWxjci5jb20uYnIvcmVwb3NpdG9yaW8vbGNyL0FDQ2VydGlzaWduTXVsdGlwbGFHNS9MYXRlc3RDUkwuY3JsMFugWaBXhlVodHRwOi8vcmVwb3NpdG9yaW8uaWNwYnJhc2lsLmdvdi5ici9sY3IvQ2VydGlzaWduL0FDQ2VydGlzaWduTXVsdGlwbGFHNS9MYXRlc3RDUkwuY3JsMB0GA1UdJQQWMBQGCCsGAQUFBwMCBggrBgEFBQcDBDCBoAYIKwYBBQUHAQEEgZMwgZAwZAYIKwYBBQUHMAKGWGh0dHA6Ly9pY3AtYnJhc2lsLmNlcnRpc2lnbi5jb20uYnIvcmVwb3NpdG9yaW8vY2VydGlmaWNhZG9zL0FDX0NlcnRpc2lnbl9NdWx0aXBsYV9HNS5wN2MwKAYIKwYBBQUHMAGGHGh0dHA6Ly9vY3NwLmNlcnRpc2lnbi5jb20uYnIwDQYJKoZIhvcNAQELBQADggIBAL81YulnHdabDOgFb8Xn98js8SJenNobFkTIVp/39HRs7iqym8uoLzHT/171GCEOQJlGTYov5tayavMgytTjiNWiuX9NaUobQiwus6a4KQQphLkRyYSTVUnlUcnYW6N/O6IQc0ZEJjvUaOd295C7Scz5sn2N3cebIodGaYFCIRGE16b431ZiOaIunwXYVzg/nVpG9J9Vy+Y45jLa7b+flKOR6VWAxdfYNYqF6eh5mCMtmmJIrt4wpTdSv3iUhwRiOZzd4zEgc0nWLFoksuwBtGPEqDp9s/bs2AKYB/4QswF7cVlnj+b2pSQ0gL2l8DaCfBtUI6oPx+k5d8JoBzJHmXmkZhI2dx3Qdlc2rA9aR3fEyDfxhw1TZsKMxakDsMQbBmR//xrjLdZkTo7fXhFhJAzJG1138/TK2sDtDHAWlV5UMosI/JaDOMeHhx/FpELKrFW7WI0CmHUl0B9fwLMGY6lqoY3avLNhAURYGfYdaEn5DH6gyNrgExZ5TLCP7HBeSpoADZbvSsp499VuEgEk0ij3+lUoKSqsmNb4t6C9QDBqKAqJqtEOEkDG7LRJF4vqCjnGsv+TreJQkwfuBT1KBy7rpPFUmwXta6ePIP9TAi/kpjz0PlgT3u/EIeYrDry6p44K9waOrgFiYu7JqPZzNzVHyC06eR2DaDWVnwPphlFA</X509Certificate></X509Data></KeyInfo></Signature></evento><retEvento versao="1.00" xmlns="http://www.portalfiscal.inf.br/nfe">
                     * <infEvento Id="ID891170007323132"><tpAmb>1</tpAmb><verAplic>AN_1.0.0</verAplic>
                     * <cOrgao>91</cOrgao>
                     * <cStat>135</cStat>
                     * <xMotivo>Evento registrado e vinculado a NF-e</xMotivo>
                     * <chNFe>35161258716523000119550000000440101885438041</chNFe>
                     * <tpEvento>210200</tpEvento>
                     * <xEvento>Confirmacao da Operacao</xEvento><nSeqEvento>1</nSeqEvento>
                     * <CNPJDest>89850341000160</CNPJDest>
                     * <dhRegEvento>2017-01-03T15:33:21-02:00</dhRegEvento>
                     * <nProt>891170007323132</nProt></infEvento></retEvento></procEventoNFe>
                     * 
                     */
                    /*
                     * <resEvento xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" versao="1.01" xmlns="http://www.portalfiscal.inf.br/nfe">
                     * <cOrgao>91</cOrgao>
                     * <CNPJ>33683111000107</CNPJ>
                     * <chNFe>35161202663810000153550380000257021733208447</chNFe>
                     * <dhEvento>2016-12-22T19:55:44-02:00</dhEvento>
                     * <tpEvento>610600</tpEvento>
                     * <nSeqEvento>1</nSeqEvento>
                     * <xEvento>Registro de Autorização de CT-e para a NF-e</xEvento>
                     * <dhRecbto>2016-12-22T19:55:44-02:00</dhRecbto>
                     * <nProt>891161760045988</nProt></resEvento>
                     */
                    $dhEvento = new \DateTime($nodom->getElementsByTagName('dhEvento')->item(0)->nodeValue);
                    $dhRecbto = new \DateTime($nodom->getElementsByTagName('dhRecbto')->item(0)->nodeValue);
                    $ev = new Event();
                    $ev->id_empresa = $this->cad->id_empresa;
                    $ev->nsu = $numnsu;
                    $ev->content = $content;
                    $ev->cnpj = $nodom->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    $ev->chNFe = $nodom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                    $ev->tpEvento = $nodom->getElementsByTagName('tpEvento')->item(0)->nodeValue;
                    $ev->nSeqEvento = $nodom->getElementsByTagName('nSeqEvento')->item(0)->nodeValue;
                    $ev->xEvento = $nodom->getElementsByTagName('xEvento')->item(0)->nodeValue;
                    $ev->dhEvento = $dhEvento->format('Y-m-d H:i:s');
                    $ev->dhRecbto = $dhRecbto->format('Y-m-d H:i:s');
                    $ev->nProt = $nodom->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $ev->save();
                    
                    $nodom = new \DOMDocument();
                    $nodom->loadXML($content);
                    $node1 = $nodom->getElementsByTagName('retDistDFeInt')->item(0);
                    $std->id_empresa = $this->cad->id_empresa;
                $std->nsu = $numnsu;
                $std->content = $content;
                    $std->cnpj = $nodom->getElementsByTagName('CNPJ')->item(0)->nodeValue;
                    $std->chNFe = $nodom->getElementsByTagName('chNFe')->item(0)->nodeValue;
                    $std->tpEvento = $nodom->getElementsByTagName('tpEvento')->item(0)->nodeValue;
                    $std->nSeqEvento = $nodom->getElementsByTagName('nSeqEvento')->item(0)->nodeValue;
                    $std->xEvento = $nodom->getElementsByTagName('descEvento')->item(0)->nodeValue;
                    $std->dhEvento = $nodom->getElementsByTagName('dhEvento')->item(0)->nodeValue;
                    $std->dhRecbto = $nodom->getElementsByTagName('dhRegEvento')->item(0)->nodeValue;
                    $std->nProt = $nodom->getElementsByTagName('nProt')->item(0)->nodeValue;
                    $std->content;
                }
            }
            sleep(5);
        }
    }
    
    public function manifestCience()
    {
        
    }
    
    public function getLastNSU()
    {
        //pega o maior numero de nsu da tabela
        $nsu = Nsu::where('id_empresa', $this->cad->id_empresa)
            ->orderBy('nsu', 'desc')
            ->first();
        $num = 0;
        if (!empty($nsu)) {
            $num = $nsu->nsu;
        }
        return $num;
    }
}
