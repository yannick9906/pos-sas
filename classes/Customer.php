<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 30.06.2016
     * Time: 21:19
     */

    namespace POS;


    class Customer {
        private $pdo, $cID, $name, $barcode;

        /**
         * Customer constructor.
         *
         * @param $cID
         * @param $name
         * @param $barcode
         */
        public function __construct($cID, $name, $barcode) {
            $this->cID = $cID;
            $this->name = $name;
            $this->barcode = $barcode;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new Customer Object from a given customer ID
         *
         * @param $cID int Customer ID
         * @return Customer
         */
        public static function fromCID($cID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_customer WHERE cID = :cid", [":cid" => $cID]);
            return new Customer($res->cID, $res->name, $res->barcode);
        }

        /**
         * Creates a new Customer Object from a given barcode
         *
         * @param $barcode string Customer Barcode
         * @return Customer
         */
        public static function fromBarcode($barcode) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_customer WHERE barcode = :barcode", [":barcode" => $barcode]);

            return new Customer($res->cID, $res->name, $res->barcode);
        }

        /**
         * checks if a barcode is in the customer db
         *
         * @param $barcode string Barcode
         * @return bool
         */
        public static function doesCustomerExist($barcode) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_customer WHERE barcode = :barcode", [":barcode" => $barcode]);
            return isset($res->cID);
        }

        /**
         * Deletes a user
         *
         * @return bool
         */
        public function delete() {
            return $this->pdo->query("DELETE FROM pos_customer WHERE cID = :cid", [":cid" => $this->cID]);
        }

        /**
         * Saves the Changes made to this object to the db
         */
        public function saveChanges() {
            $this->pdo->queryUpdate("pos_customer",
                ["name" => utf8_decode($this->name),
                 "barcode" => utf8_decode($this->barcode)],
                "cID = :cid",
                ["cid" => $this->cID]
            );
        }

        /**
         * Creates a new customer from the give attribs
         *
         * @param $name    string Customer name
         * @param $barcode string Customer barcode
         */
        public static function createNew($name, $barcode) {
            $pdo = new PDO_MYSQL();
            $pdo->queryInsert("pos_customer",
                ["name" => $name,
                 "barcode" => $barcode]
            );
        }

        /**
         * Makes this class as an array to use for tables etc.
         *
         * @return array
         */
        public function asArray() {
            if($this->name != "") $name = $this->name;
            else $name = "Kunde #".$this->cID;
            return [
                "cID" => $this->cID,
                "name" => $name,
                "barcode" => $this->barcode
            ];
        }

        /**
         * @return int
         */
        public function getCID() {
            return $this->cID;
        }

        /**
         * @param int $cID
         */
        public function setCID($cID) {
            $this->cID = $cID;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * @return string
         */
        public function getBarcode() {
            return $this->barcode;
        }

        /**
         * @param string $barcode
         */
        public function setBarcode($barcode) {
            $this->barcode = $barcode;
        }
    }