<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 30.06.2016
     * Time: 21:19
     */

    namespace POS;


    class Item {
        private $pdo, $iID, $itemName, $inStock, $priceBuy, $priceSell, $barcode;

        /**
         * Item constructor.
         *
         * @param int $iID
         * @param string $itemName
         * @param int $inStock
         * @param int $priceBuy
         * @param int $priceSell
         * @param string $barcode
         */
        public function __construct($iID, $itemName, $inStock, $priceBuy, $priceSell, $barcode) {
            $this->iID = $iID;
            $this->itemName = utf8_encode($itemName);
            $this->inStock = $inStock;
            $this->priceBuy = $priceBuy;
            $this->priceSell = $priceSell;
            $this->barcode = $barcode;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Constructs a new object from a specific iID
         *
         * @param int $iID iID
         * @return Item Constructed Item
         */
        public static function fromIID($iID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_item WHERE iID = :iid", [":iid" => $iID]);
            return new Item($res->iID, $res->itemName, $res->inStock, $res->priceBuy, $res->priceSell, $res->barcode);
        }

        /**
         * Constructs a new object from a specific barcode
         *
         * @param string $barcode Barcode
         * @return Item Constructed Item
         */
        public static function fromBarcode($barcode) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_item WHERE barcode = :barcode", [":barcode" => $barcode]);
            return new Item($res->iID, $res->itemName, $res->inStock, $res->priceBuy, $res->priceSell, $res->barcode);
        }

        /**
         * Returns all entries matching the search and the page
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         *
         * @return array Normal dict array with data
         */
        public static function getList($page = 1, $pagesize = 75, $search = "") {

        }

        /**
         * @see getList()
         * but you'll get Objects instead of an array
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         *
         * @return Item[]
         */
        public static function getListObjects($page, $pagesize, $search) {

        }

        /**
         * Creates a new item in DB.
         *
         * @param string $itemName
         * @param int $inStock
         * @param int $priceBuy
         * @param int $priceSell
         * @param string $barcode
         */
        public static function createNew($itemName, $inStock, $priceBuy, $priceSell, $barcode) {
            $pdo = new PDO_MYSQL();
            $pdo->query("insert into pos_item(itemName, inStock, priceBuy, priceSell, barcode) values (:name, :stock, :priceBuy, :priceSell, :barcode)",
                [":name" => $itemName, ":inStock" => $inStock, ":priceBuy" => $priceBuy, ":priceSell" => $priceSell, ":barcode" => $barcode]);
        }

        /**
         * Save the changes made to an instance of this class into the DB.
         */
        public function saveChanges() {
            $this->pdo->query("update pos_item set itemName = :name, inStock = :inStock, priceBuy = :priceBuy, priceSell = :priceSell, barcode = :barcode where iID = :iid",
                [":iid" => $this->iID, ":name" => $this->itemName, ":inStock" => $this->inStock, ":priceBuy" => $this->priceBuy, ":priceSell" => $this->priceSell, ":barcode" => $this->barcode]);
        }

        /**
         * Turns this instance into an array
         *
         * @return array Array
         */
        public function asArray() {
            return [
                "iID" => $this->iID,
                "itemName" => $this->itemName,
                "inStock" => $this->inStock,
                "priceBuy" => $this->priceBuy,
                "priceSell" => $this->priceSell,
                "barcode" => $this->barcode
            ];
        }

        /**
         * @return int
         */
        public function getIID() {
            return $this->iID;
        }

        /**
         * @param int $iID
         */
        public function setIID($iID) {
            $this->iID = $iID;
        }

        /**
         * @return string
         */
        public function getItemName() {
            return $this->itemName;
        }

        /**
         * @param string $itemName
         */
        public function setItemName($itemName) {
            $this->itemName = $itemName;
        }

        /**
         * @return int
         */
        public function getInStock() {
            return $this->inStock;
        }

        /**
         * @param int $inStock
         */
        public function setInStock($inStock) {
            $this->inStock = $inStock;
        }

        /**
         * @return int schlopos
         */
        public function getPriceBuy() {
            return $this->priceBuy;
        }

        /**
         * @param int $priceBuy schlopos
         */
        public function setPriceBuy($priceBuy) {
            $this->priceBuy = $priceBuy;
        }

        /**
         * @return int schlopos
         */
        public function getPriceSell() {
            return $this->priceSell;
        }

        /**
         * @param int $priceSell schlopos
         */
        public function setPriceSell($priceSell) {
            $this->priceSell = $priceSell;
        }

        /**
         * @return string Barcode
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