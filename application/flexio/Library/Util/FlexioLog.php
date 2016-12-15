<?php
namespace Flexio\Library\Util;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


class FlexioLog
{
    private $logger;
    protected $stream;
    protected $firephp;
    protected $debugBar;

    public function __construct()
    {
        $this->stream = new StreamHandler('./public/logs/querys-'.date('Y-m-d').'.log', Logger::INFO);
        $this->firephp = new FirePHPHandler();
        $this->logger = new Logger('DBLOG: ');
        $this->logger->pushHandler($this->stream);
    }

    public function queryLog($mensaje)
    {
        $this->logger->addInfo($mensaje);

    }

    function debugBar($mensaje){
        $this->debugBar =  new StandardDebugBar();
        $this->debugBar->addCollector(new \DebugBar\DataCollector\MessagesCollector("database"));
        $this->debugBar["database"]->addMessage($mensaje);

        //$pdo = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));
        //$pdo = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));
        //$debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));
        //$pdoCollector = new DebugBar\DataCollector\PDO\PDOCollector();
        //$pdoCollector->addConnection($pdo, 'read-db');
        //$debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector());
        //$debugbar['messages']->info('hello world');
    }
}
