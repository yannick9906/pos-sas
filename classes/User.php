<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 30.06.2016
     * Time: 21:19
     */

    namespace POS;


    use PDO;

    class User {
        private $pdo, $uID, $uName, $uRealname, $uPassHash;

        /**
         * User constructor.
         *
         * @param int $uID
         * @param string $uName
         * @param string $uRealname
         * @param string $uPassHash
         */
        public function __construct($uID, $uName, $uRealname, $uPassHash) {
            $this->uID = $uID;
            $this->uName = utf8_encode($uName);
            $this->uRealname = utf8_encode($uRealname);
            $this->uPassHash = $uPassHash;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new User Object from a give user ID
         *
         * @param $uID int User ID
         * @return User
         */
        public static function fromUID($uID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_user WHERE uID = :uid", [":uid" => $uID]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash);
        }
        /**
         * Creates a new User Object from a give username
         *
         * @param $uName string Username
         * @return User
         */
        public static function fromUName($uName) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_user WHERE username = :uname", [":uname" => $uName]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash);
        }

        /**
         * Compares a md5() hash with the given Hash from db
         *
         * @param $hash string md5-hash
         * @return bool
         */
        public function comparePWHash($hash) {
            if($hash == $this->uPassHash) {
                echo $hash . "<br/>" . $this->uPassHash;
                return true;
            } else {
                echo $hash . "<br/>" . $this->uPassHash;
                return false;
            }
        }


        /**
         * Makes this class as an array to use for tables etc.
         *
         * @return array
         */
        public function asArray() {
            return [
                "id" => $this->uID,
                "usrname" => $this->uName,
                "realname" => $this->uRealname
            ];
        }


        /**
         * Makes this class as an string to use for debug only
         *
         * @return string
         */
        public function toString() {
            return
                "id:        ".$this->uID."\n".
                "usrname:   ".$this->uName."\n".
                "realname:  ".$this->uRealname."\n";
        }

        /**
         * checks if a username is in the user db
         *
         * @param $uName string Username
         * @return bool
         */
        public static function doesUserNameExist($uName) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_user WHERE username = :uname", [":uname" => $uName]);
            return isset($res->uID);
        }

        /**
         * Returns all users as a array of Userobjects from db
         *
         * @param string $sort
         * @param string $filter
         * @return User[]
         */
        public static function getAllUsers($sort = "", $filter = "") {
            $pdo = new PDO_MYSQL();
            $stmt = $pdo->queryMulti('SELECT uID FROM entrance_user '.UFILTERING[$filter].' '.USORTING[$sort]);
            return $stmt->fetchAll(PDO::FETCH_FUNC, "\\Entrance\\User::fromUID");
        }

        /**
         * Deletes a user
         *
         * @return bool
         */
        public function delete() {
            return $this->pdo->query("DELETE FROM pos_user WHERE uID = :uid", [":uid" => $this->uID]);
        }

        /**
         * Saves the Changes made to this object to the db
         */
        public function saveChanges() {
            $this->pdo->query("UPDATE pos_user SET realname = :realname, passhash = :Passwd, username = :Username WHERE uID = :uID LIMIT 1",
                [":realname" => $this->uRealname, ":Passwd" => $this->uPassHash, ":Username" => $this->uName, ":uID" => $this->uID]);
        }

        /**
         * Creates a new user from the give attribs
         *
         * @param $username string Username
         * @param $realname string Realname of the user
         * @param $passwdhash string md5 Hash of Password
         * @return User The new User as an Object
         */
        public static function createUser($username, $realname, $passwdhash) {
            $pdo = new PDO_MYSQL();
            $pdo->query("INSERT INTO pos_user(username, passhash, realname) VALUES (:Username, :Passwd, :Realname)",
                [":Username" => $username, ":Realname" => $realname, ":Passwd" => md5($passwdhash)]);
            return self::fromUName($username);
        }

        /**
         * @return int
         */
        public function getUID() {
            return $this->uID;
        }

        /**
         * @param int $uID
         */
        public function setUID($uID) {
            $this->uID = $uID;
        }

        /**
         * @return string
         */
        public function getUName() {
            return $this->uName;
        }

        /**
         * @param string $uName
         */
        public function setUName($uName) {
            $this->uName = $uName;
        }

        /**
         * @return string
         */
        public function getURealname() {
            return $this->uRealname;
        }

        /**
         * @param string $uRealname
         */
        public function setURealname($uRealname) {
            $this->uRealname = $uRealname;
        }

        /**
         * @return string
         */
        public function getUPassHash() {
            return $this->uPassHash;
        }

        /**
         * @param string $uPassHash
         */
        public function setUPassHash($uPassHash) {
            $this->uPassHash = $uPassHash;
        }
    }