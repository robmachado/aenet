<?php

namespace Aenet\NFe\Common;

use Exception;

class ServerMonitor
{
    /**
     * @var string
     */
    public $osname = '';
    /**
     * @var string
     */
    public $hostname = '';
    /**
     * @var string
     */
    public $osrelease = '';
    /**
     * @var string
     */
    public $osversion = '';
    /**
     * @var string
     */
    public $kernel = '';
    /**
     * @var string
     */
    public $ostype = '';
    /**
     * @var int
     */
    public $servercores = 1;
    /**
     * @var array
     */
    public $load = [];
    /**
     * @var float
     */
    public $httpconnections = 0;
    /**
     * @var float
     */
    public $totalservermemory = 0;
    /**
     * @var float
     */
    public $freeservermemory = 0;
    /**
     * @var float
     */
    public $memoryusage = 0;
    /**
     * @var float
     */
    public $totalswap = 0;
    /**
     * @var float
     */
    public $swapusage = 0;
    /**
     * @var float
     */
    public $diskusage = 0;
    /**
     * @var int
     */
    public $uptimedays = 0;
    /**
     * @var int
     */
    public $uptimehours = 0;
    /**
     * @var int
     */
    public $uptimeminutes = 0;
    
    public $phpmemoryallocate = '';
    public $phpmemoryusage = '';
    public $phppeakemoryusage = '';
    
    private $os = '';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->os = PHP_OS;
        if ($this->os == 'WINNT') {
            throw new \Exception('This class does not work in windows environment.');
        }
        $this->systemInfo();
        $this->systemCores();
        $this->serverUptime();
        $this->serverUptime();
        $this->systemLoad();
        $this->serverMemoryUsage();
        $this->correntMemoryUsage();
    }
    
    /**
     * Gets operational system info
     * @return void
     */
    protected function systemInfo()
    {
        $this->osname = php_uname('s');
        $this->hostname = php_uname('n');
        $this->osrelease = php_uname('r');
        $this->osversion = php_uname('v');
        $this->ostype = php_uname('m');
    }
    
    /**
     * Get the number of cores of the processor
     * @return void
     */
    protected function systemCores()
    {
        switch ($this->os) {
            case ('Linux'):
                $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
                break;
            case ('Freebsd'):
                $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
                break;
        }
        $cpuCoreNo = intval(trim(shell_exec($cmd)));
        $this->servercores = empty($cpuCoreNo) ? 1 : $cpuCoreNo;
    }
   
   /**
    * Get system load as percentage
    * NOTE: This search must be done with a minimum interval of 15 minutes
    * @return array
    */
    public function systemLoad()
    {
        $rs = sys_getloadavg();
        foreach ($rs as $key => $value) {
            $this->load[$key] = round(($value*100)/$this->servercores, 2);
        }
        return $this->load;
    }
    
    /**
     * Returns a number of http active conections
     * @return int
     */
    public function httpConnections()
    {
        if (function_exists('exec')) {
            $www_total_count = 0;
            $unique = [];
            @exec('netstat -an | egrep \':80|:443\' | awk \'{print $5}\' | grep -v \':::\*\' |  grep -v \'0.0.0.0\'', $results);
            foreach ($results as $result) {
                $array = explode(':', $result);
                $www_total_count ++;
                if (preg_match('/^::/', $result)) {
                    $ipaddr = $array[3];
                } else {
                    $ipaddr = $array[0];
                }
                if (!in_array($ipaddr, $unique)) {
                    $unique[] = $ipaddr;
                    $www_unique_count ++;
                }
            }
            unset($results);
            $this->httpconnections = count($unique);
            return $this->httpconnections;
        }
        return 0;
    }
    
    /**
     * Return the server memory usage as a percentage
     * @return float
     */
    public function serverMemoryUsage()
    {
        $free = shell_exec('free -btl');
        $free = (string)trim($free);
        $free = str_replace("  ", " ", $free);
        $free = str_replace("  ", " ", $free);
        $free = str_replace("  ", " ", $free);
        $free = str_replace("  ", " ", $free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $swap = explode(" ", $free_arr[4]);
        //$swap = array_filter($swap);
        //$swap = array_merge($swap);
        $this->totalservermemory = $this->formatBytes($mem[1], 0);
        $this->freeservermemory = $this->formatBytes($mem[3], 0);
        $this->totalswap = $this->formatBytes($mem[1], 0);
        $swap_usage = $swap[2] / $swap[1] * 100;
        $this->swapusage = $swap_usage;
        $memory_usage = $mem[2] / $mem[1] * 100;
        $this->memoryusage = $memory_usage;
        return $memory_usage;
    }
    
    /**
     * Converts bytes into their multiples
     * @param int $size
     * @param int $precision
     * @return string
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'kB', 'MB', 'GB', 'TB', 'PT');
        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
    
    /**
     * Returns the amount of disk usage as a percentage
     * @return float
     */
    public function diskUsage()
    {
        $disktotal = disk_total_space('/');
        $diskfree = disk_free_space('/');
        $diskuse = round(100-(($diskfree/$disktotal)*100), 2);
        $this->diskusage = $diskuse;
        return $diskuse;
    }
    
    /**
     * Returns the server uptime
     * @return void
     */
    protected function serverUptime()
    {
        $uptimestring = file_get_contents('/proc/uptime');
        $ticks = explode(" ", $uptimestring);
        $min = $ticks[0]/60;
        $hours = $min/60;
        $this->uptimedays = floor($hours/24);
        $this->uptimehours = floor($hours-($days*24));
        $this->uptimeminutes = floor($min-($days*60*24)-($hours*60));
    }
    
    /**
     * Get kernel version
     * @return void
     */
    protected function kernelVersion()
    {
        $kernel = explode(' ', file_get_contents('/proc/version'));
        $this->kernel = $kernel[2];
    }
    
    /**
     * Get number of processes
     * @return int
     */
    public function numberProcesses()
    {
        $proc_count = 0;
        $dh = opendir('/proc');
        while ($dir = readdir($dh)) {
            if (is_dir('/proc/' . $dir)) {
                if (preg_match('/^[0-9]+$/', $dir)) {
                    $proc_count ++;
                }
            }
        }
        return $proc_count;
    }
    
    /**
     * Get corrent memory usage by PHP
     * @return void
     */
    protected function correntMemoryUsage()
    {
        $mem = memory_get_usage(true);
        $this->phpmemoryallocate = $this->formatBytes($mem, 0);
        $mem = memory_get_usage(false);
        $this->phpmemoryusage = $this->formatBytes($mem, 0);
        $mem = memory_get_peak_usage(false);
        $this->phppeakmemoryusage = $this->formatBytes($mem, 0);
    }
}
