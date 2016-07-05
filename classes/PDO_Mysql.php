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
        private $dbname = 'pos';
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

        public function queryPagedList($tablename, $startElem, $endElem, $searchableFields, $search) {
            $db = $this->connect();

            if($search != "") {
                $lastField = $searchableFields[sizeof($searchableFields)-1];
                $bindString = '';
                foreach ($searchableFields as $field) {
                    $bindString .= $field;
                    $bindString .= ($field === $lastField ? '' : '," ",');
                }

                $stmt = $db->prepare("SELECT * FROM " . $tablename . " WHERE lower(concat(" . $bindString . ")) LIKE lower(concat('%',:search,'%')) LIMIT :start,:end");
                $stmt->bindValue(":search", $search, PDO::PARAM_STR);
            } else {
                $stmt = $db->prepare("SELECT * FROM " . $tablename . " LIMIT :start,:end");
            }
            $stmt->bindValue(":start", $startElem, PDO::PARAM_INT);
            $stmt->bindValue(":end", $endElem, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt;
        }

        /**
         * Makes a INSERT query
         *
         * @param string $table Tablename
         * @param array $fields Fields to insert ["fieldname" => "data"]
         * @return mixed
         */
        public function queryInsert($table, $fields) {
            $db = $this->connect();
            end($fields); $lastField = key($fields);
            $bindString = ' ';
            foreach($fields as $field => $data) {
                $bindString .= $field . '=:' . $field;
                $bindString .= ($field === $lastField ? ' ' : ',');
            }

            $stmt = $db->prepare("insert into " . $table . " set " . $bindString);
            $this->bindValues($stmt, $fields);

            $stmt->execute();
            return $stmt->fetchObject();
        }

        /**
         * Makes a UPDATE query
         *
         * @param string $table  Tablename
         * @param array  $fields Fields to insert ["fieldname" => "data"]
         * @param string $where  Custom where clause
         * @param array  $wherefields Fields for where clause ["fieldname" => "data"]
         * @return mixed
         */
        public function queryUpdate($table, $fields, $where, $wherefields) {
            $db = $this->connect();
            end($fields); $lastField = key($fields);
            $bindString = ' ';
            foreach($fields as $field => $data) {
                $bindString .= $field . '=:' . $field;
                $bindString .= ($field === $lastField ? ' ' : ',');
            }

            $stmt = $db->prepare("update " . $table . " set " . $bindString . " where " . $where);
            $this->bindValues($stmt, $fields);
            $this->bindValues($stmt, $wherefields);

            $stmt->execute();
            return $stmt->fetchObject();
        }

        /**
         * Bind the fields from an array to a statement
         *
         * @param \PDOStatement $stmt
         * @param array $fields
         */
        private function bindValues($stmt, $fields) {
            foreach($fields as $field => $data) {
                if(is_int($data)) {
                    $stmt->bindValue($field, $data, PDO::PARAM_INT);
                } elseif(is_bool($data)) {
                    $stmt->bindValue($field, $data, PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue($field, $data, PDO::PARAM_STR);
                }
            }
        }
    }