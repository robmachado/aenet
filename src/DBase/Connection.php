<?php

namespace Aenet\NFe\DBase;

use Illuminate\Database\Capsule\Manager as Capsule;
use stdClass;

class Connection
{
    /**
     * @var Capsule
     */
    public $capsule;
    /**
     * @var string
     */
    private $driver    = 'mysql';
    /**
     * @var string
     */
    private $host      = 'localhost';
    /**
     * @var string
     */
    private $database  = 'aenet_nfe';
    /**
     * @var string
     */
    private $username  = 'root';
    /**
     * @var string
     */
    private $password  = 'monitor5';
    /**
     * @var string
     */
    private $charset   = 'utf8';
    /**
     * @var string
     */
    private $collation = 'utf8_unicode_ci';
    /**
     * @var string
     */
    private $prefix    = '';
    
    /**
     * Constructor may be recive configurations data in a stdClass
     * @param stdClass $config
     */
    public function __construct(stdClass $config = null)
    {
        if (!empty($config)) {
            $this->setCharSet($config->charset);
            $this->setCollation($config->collation);
            $this->setUserName($config->username);
            $this->setPassword($config->password);
            $this->setDriver($config->driver);
            $this->setHost($config->host);
            $this->setPrefix($$config->prefix);
        }
    }
    
    /**
     * Connect database with parameter class
     */
    public function connect()
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver'    => $this->driver,
            'host'      => $this->host,
            'database'  => $this->database,
            'username'  => $this->username,
            'password'  => $this->password,
            'charset'   => $this->charset,
            'collation' => $this->collation,
            'prefix'    => $this->prefix,
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }
    
    
    /**
     * Disconnect database
     */
    public function disconnect()
    {
        $this->capsule = null;
    }
    
    /**
     * Set charset parameter
     * @param string $charset
     * @return void
     */
    public function setCharSet($charset = '')
    {
        if (!empty($charset)) {
            $this->charset = $charset;
        }
    }

    /**
     * Set collation parameter
     * @param string $collation
     * @return void
     */
    public function setCollation($collation = '')
    {
        if (!empty($collation)) {
            $this->collation = $collation;
        }
    }
    
    /**
     * Set username parameter
     * @param string $username
     * @return void
     */
    public function setUserName($username = '')
    {
        if (!empty($username)) {
            $this->username = $username;
        }
    }
    
    /**
     * Set password parameter
     * @param string $password
     * @return void
     */
    public function setPassword($password = '')
    {
        if (!empty($password)) {
            $this->password = $password;
        }
    }
    
    /**
     * Set driver parameter
     * @param string $driver
     * @return void
     */
    public function setDriver($driver = '')
    {
        if (!empty($driver)) {
            $this->driver = $driver;
        }
    }

    /**
     * Set host parameter
     * IP ou domain name
     * @param string $host
     * @return void
     */
    public function setHost($host = '')
    {
        if (!empty($host)) {
            $this->host = $host;
        }
    }
    
    /**
     * Set prefix parameter
     * @param string $prefix
     * @return void
     */
    public function setPrefix($prefix = '')
    {
        if (!empty($prefix)) {
            $this->prefix = $prefix;
        }
    }
}
