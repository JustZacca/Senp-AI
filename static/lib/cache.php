<?php
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Jikan\Jikan;

class cache
{
    public $driver;
    public $connection;

    public function __construct()
    {
        $this->driver = new Mysql([
            'database' => 'senp_AI',
            'username' => 'remote',
            'password' => '11221348Was'
        ]);
        $this->connection = new Connection([
            'driver' => $this->driver
        ]);
    }

    public function cacheAnime($ani)
    {
        $this->setID();
        try {
            $this->connection->insert(
                'AnimeCache',
                ['ID_Mal' => $ani->getID(), 'Titolo' => $ani->getTitle(), 'Tags' => $ani->getTags(), 'Genere' => $ani->getGenere(), 'IMG' =>$ani->getIMG(), 'Source' =>$ani->getSource(),'Format' => $ani->getFormat()]
            );
            return true;
        } catch (Exception $var) {
            return false;
        }
    }
}
