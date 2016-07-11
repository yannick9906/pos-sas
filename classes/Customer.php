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
         * Returns all Items bought by this customer
         *
         * @return array Items
         */
        public function getAllItems() {
            $stmt = $this->pdo->queryMulti("select * from `pos_receipt-item` where cID = :cid", [":cid" => $this->cID]);
            $list = [];
            $inList = [];
            while($row = $stmt->fetchObject()) {
                $item = Item::fromIID($row->iID);
                if(in_array($row->iID, $inList)) {
                    $list[$row->iID]["amount"]++;
                } else {
                    $array = [
                        "iID" => $row->iID,
                        "itemName" => $item->getItemName(),
                        "priceDeposit" => $item->getPriceDeposit(),
                        "priceSell" => $item->getPriceSell(),
                        "priceBuy" => $item->getPriceBuy(),
                        "amount" => 1
                    ];
                    array_push($inList, $row->iID);
                    $list[$row->iID] = $array;
                }
            }
            $newlist = [];
            foreach ($list as $value) {
                array_push($newlist, $value);
            }
            return $newlist;
        }

        /**
         * Get all items that are left a deposit
         *
         * @return array Items
         */
        public function getDepositItems() {
            $stmt = $this->pdo->queryMulti("select * from `pos_receipt-item` where cID = :cid and itemDeposit = 1 and itemDepositPaid = 0", [":cid" => $this->cID]);
            $list = [];
            $inList = [];
            while($row = $stmt->fetchObject()) {
                $item = Item::fromIID($row->iID);
                if(in_array($row->iID, $inList)) {
                    $list[$row->iID]["amount"]++;
                } else {
                    $array = [
                        "iID" => $row->iID,
                        "itemName" => $item->getItemName(),
                        "priceDeposit" => $item->getPriceDeposit(),
                        "amount" => 1
                    ];
                    array_push($inList, $row->iID);
                    $list[$row->iID] = $array;
                }
            }
            $newlist = [];
            foreach ($list as $value) {
                array_push($newlist, $value);
            }
            return $newlist;
        }

        /**
         * Pays a deposit
         *
         * @param $barcode The Items barcode
         * @return bool If it worked
         */
        public function depositItem($barcode) {
            $stmt = $this->pdo->queryMulti("select * from `pos_receipt-item` where cID = :cid and itemDeposit = 1 and itemDepositPaid = 0", [":cid" => $this->cID]);
            $list = [];
            $inList = [];
            while($row = $stmt->fetchObject()) {
                $item = Item::fromIID($row->iID);
                if(in_array($row->iID, $inList)) {
                    $list[$row->iID]["amount"]++;
                } else {
                    $array = [
                        "id" => $row->id,
                        "iID" => $row->iID,
                        "itemName" => $item->getItemName(),
                        "priceDeposit" => $item->getPriceDeposit(),
                        "barcode" => $item->getBarcode(),
                        "amount" => 1
                    ];
                    array_push($inList, $row->iID);
                    $list[$row->iID] = $array;
                }
            }
            foreach ($list as $value) {
                if($value["barcode"] == $barcode) {
                    $this->pdo->queryUpdate("pos_receipt-item",
                        ["itemDepositPaid" => 1],
                        "id = :id", ["id" => $value["id"]]
                    );
                    return true;
                }
            }
            return false;
        }

        /**
         * Returns the amount of Deposit is left on this customer
         *
         * @return int Amount in Schlopo
         */
        public function getDepositValueLeft() {
            $items = $this->getDepositItems();
            $value = 0;
            foreach ($items as $item) {
                $value += $item["priceDeposit"] * $item["amount"];
            }
            return $value;
        }

        /**
         * Returns the amount of Money made with this customer
         *
         * @return int Amount in Schlopo
         */
        public function getBoughtValue() {
            $items = $this->getAllItems();
            $value = 0;
            foreach ($items as $item) {
                $value += ($item["priceSell"]-$item["priceBuy"]) * $item["amount"];
            }
            return $value;
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
                "barcode" => $this->barcode,
                "value" => $this->getBoughtValue(),
                "depositLeft" => $this->getDepositValueLeft()
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