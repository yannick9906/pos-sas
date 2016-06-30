<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 06.03.2016
     * Time: 18:53
     */
    namespace POS;
    use PDO;
    require_once 'passwords.php'; //DB Pdw
    class PDO_MYSQL {
        /**
         * Define global vars
         *
         * @var string host, pass, user, dbname
         * @var int port
         */
        /*private $host   = 'localhost';
        private $port   = 3306;
        private $pass   = "";
        private $user   = 'Chaos234sql34';
        private $dbname = 'Chaos234sql34';
    */
        private $host   = 'localhost';
        private $port   = 3306;
        private $pass   = "";
        private $user   = 'root';
        private $dbname = 'entrance';
        /**
         * @return PDO PDO-Object
         */
        protected function connect() {
            $this->pass = getMysqlPasskey();
            return new PDO('mysql:host='.$this->host.';port='.$this->port.';dbname='.$this->dbname,$this->user,$this->pass);
        }
        public function query($query, $array = []) {
            $db = $this->connect();
            $stmt = $db->prepare($query);
            if (!empty($array)) $stmt->execute($array);
            else $stmt->execute();
            return $stmt->fetchObject();
        }
        public function queryMulti($query, $array = []) {
            $db = $this->connect();
            $stmt = $db->prepare($query);
            if (!empty($array)) $stmt->execute($array);
            else $stmt->execute();
            return $stmt;
        }
    }