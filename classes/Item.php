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
            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("pos_item", $startElem, $endElem, ["itemName","barcode"], $search);

            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                array_push($hits["items"], [
                    "iID" => $row->iID,
                    "itemName" => utf8_encode($row->itemName),
                    "inStock" => $row->inStock,
                    "priceBuy" => $row->priceBuy,
                    "priceSell" => $row->priceSell,
                    "check" => md5($row->iID+$row->itemName+$row->inStock+$row->priceBuy+$row->priceSell)
                ]);
            }
            return $hits;
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
            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("pos_item", $startElem, $endElem, ["itemName","barcode"], $search);
            
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new Item(
                    $row->iID,
                    $row->itemName,
                    $row->inStock,
                    $row->priceBuy,
                    $row->priceSell,
                    $row->barcode)
                );
            }
            return $hits;
        }

        /**
         * Returns the array stub for the getLists() method
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         * @return array
         */
        public static function getListMeta($page, $pagesize, $search) {
            $pdo = new PDO_MYSQL();
            if($search != "") $res = $pdo->query("select count(*) as size from pos_item where lower(concat(itemName,' ',barcode)) like lower(:search)", [":search" => "%".$search."%"]);
            else $res = $pdo->query("select count(*) as size from pos_item");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "items" => []
            ];
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
            $pdo->queryInsert("pos_item",
                ["name" => $itemName,
                 "inStock" => $inStock,
                 "priceBuy" => $priceBuy,
                 "priceSell" => $priceSell,
                 "barcode" => $barcode]
            );
        }

        /**
         * Save the changes made to an instance of this class into the DB.
         */
        public function saveChanges() {
            $this->pdo->queryUpdate("pos_item",
                ["iid" => $this->iID,
                 "name" => $this->itemName,
                 "inStock" => $this->inStock,
                 "priceBuy" => $this->priceBuy,
                 "priceSell" => $this->priceSell,
                 "barcode" => $this->barcode],
                "iID = :iid",
                ["iid" => $this->iID]
            );
        }

        /**
         * Deletes this item from db
         */
        public function delete() {
            $this->pdo->query("delete from pos_item where iID = :iid",
                [":iid" => $this->iID]);
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