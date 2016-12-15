<?php
namespace Flexio\Library\StatsD;
use League\StatsD\Client;

class FlexioStats{

  public $cliente;
  public $host;
  public $port;
  public $namespace;

  function __construct($host=null,$port=null,$namespace=null){

    $this->host = '127.0.0.1';
    $this->port = 8125;
    $this->namespace = 'factura-compra';

  }

  function configuracion(){
      $cliente = new League\StatsD\Client();
        $cliente->configure(array(
         'host' => $this->host,
         'port' => $this->port,
         'namespace' => $this->namespace
    ));

    return $cliente;
  }

}
