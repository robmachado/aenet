<?php


namespace Aenet\NFe\Common;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Flags
{
    private static $filesystem;
    private static $timelimit = 59; //time in minutes
    
    /**
     * Cria o arquivo de controle do job
     * se o arquivo foi criado retorna true
     * caso contrario retorna false, pois o arquivo já existe
     * @param string $jobname
     */
    public static function set($jobname)
    {
        if (!self::find($jobname)) {
            self::$filesystem->put("flag_$jobname", date('Y-m-d H:i:s'));
            return true;
        }
        return false;
    }
    
    /**
     * Apaga o arquivo de controle do job
     * @param type $jobname
     */
    public static function reset($jobname)
    {
        if (self::find($jobname)) {
            self::$filesystem->delete("flag_$jobname");
        }
        return true;
    }
    
    /**
     * Procura pelo arquivo de controle do job
     * se não for encontrado retorna false, e se for encontrado retorna true
     * NOTA: irá retornar false caso o tempo de criação do arquivo supere o
     * limite estabelecido
     * @param string $jobname
     * @return boolean
     */
    protected static function find($jobname)
    {
        $folder = realpath(__DIR__ . "/../../storage/");
        $adapter = new Local($folder);
        self::$filesystem = new Filesystem($adapter);
        if (self::$filesystem->has("flag_$jobname")) {
            $timestamp = self::$filesystem->getTimestamp("flag_$jobname");
            if (self::checkExpireTime($timestamp)) {
                self::$filesystem->delete("flag_$jobname");
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Verifica se o arquivo de controle foi criado a mais tempo que o limite
     * @param integer $timestamp
     * @return boolean
     */
    protected static function checkExpireTime($timestamp)
    {
        $dtNow = new \DateTime();
        $dtFile = new \DateTime();
        $dtFile->setTimestamp($timestamp);
        $interval = $dtNow->diff($dtFile);
        $min = (int) $interval->format('%i');
        if ($min > self::$timelimit) {
            //o tempo expirou, o arquivo de controle foi criado a mais tempo
            //que o limite setado, isso não pode ser considerado como
            //algo commum em principio, mas esse tempo pode e deve ser ajustado
            //conforme o processo for melhorado
            return true;
        }
        return false;
    }
}
