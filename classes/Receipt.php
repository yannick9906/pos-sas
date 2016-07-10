<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 30.06.2016
     * Time: 21:19
     */

    namespace POS;


    class Receipt {
        private $pdo, $rID, $cID, $timestamp, $totalBuy, $totalSell, $totalDeposit;

        /**
        * Receipt constructor.
        *
        * @param $rID
        * @param $cID
        * @param $timestamp
        * @param $totalBuy
        * @param $totalSell
        */
        public function __construct($rID, $cID, $timestamp, $totalBuy, $totalSell, $totalDeposit) {
            $this->rID = $rID;
            $this->cID = $cID;
            $this->timestamp = strtotime($timestamp);
            $this->totalBuy = $totalBuy;
            $this->totalSell = $totalSell;
            $this->totalDeposit = $totalDeposit;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new Receipt Object from a give receipt ID
         *
         * @param $rID int Receipt ID
         * @return Receipt
         */
        public static function fromRID($rID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_receipt WHERE rID = :rid", [":rid" => $rID]);
            return new Receipt($res->rID, $res->cID, $res->timestamp, $res->totalBuy, $res->totalSell, $res->totalDeposit);
        }

        /**
         * Creates a new receipt from the give attribs
         *
         * @param $cID int The Customer
         * @return Receipt
         */
        public static function createNew($cID) {
            $pdo = new PDO_MYSQL();
            $pdo->queryInsert("pos_receipt",
                [
                   "cID" => $cID,
                   "timestamp" => date("Y-m-d H:i:s"),
                   "totalBuy" => 0,
                   "totalSell" => 0,
                   "totalDeposit" => 0
                ]);
            $res = $pdo->query("select rID from pos_receipt order by rID desc limit 1");
            return self::fromRID($res->rID);
        }

        /**
         * Adds an Item to this receipt
         *
         * @param $item Item
         */
        public function addItem($item) {
            $this->totalDeposit += $item->getPriceDeposit();
            $this->totalSell += $item->getPriceSell();
            $this->totalBuy += $item->getPriceBuy();
            $this->pdo->queryInsert("pos_receipt-item",
                [
                    "rID" => $this->rID,
                    "iID" => $item->getIID(),
                    "itemDeposit" => $item->getPriceDeposit(),
                    "itemPrice" => $item->getPriceSell(),
                    "itemDepositPaid" => 0
                ]);
            $this->saveChanges();
        }

        public function getItems() {
            $res = $this->pdo->queryMulti("select * from `pos_receipt-item` where rID = :rid", [":rid" => $this->rID]);
            $inList = [];
            $list = [];
            while($row = $res->fetchObject()) {
                $item = Item::fromIID($row->iID);
                if(in_array($row->iID, $inList)) {
                    $list[$res->iID]["amount"]++;
                } else {
                    $array = [
                        "iID" => $row->iID,
                        "itemName" => $item->getItemName(),
                        "priceSell" => $item->getPriceSell(),
                        "amount" => 1
                    ];
                    array_push($inList, $row->iID);
                    $list[$res->iID] = $array;
                }
            }
            $newlist = [];
            foreach ($list as $value) {
                array_push($newlist, $value);
            }
            return $newlist;
        }

        public function saveChanges() {
            $this->pdo->queryUpdate(
                "pos_receipt",
                ["cID" => $this->cID,
                    "timestamp" => date("Y-m-d H:i:s", $this->timestamp),
                    "totalBuy" => $this->totalBuy,
                    "totalSell" => $this->totalSell,
                    "totalDeposit" => $this->totalDeposit],
                "rID = :rID",
                ["rID" => $this->rID]
            );
        }

        /**
         *  Turns this instance into a array
         *
         * @return array
         */
        public function asArray() {
            return [
                "rID" => $this->rID,
                "cID" => $this->cID,
                "timestamp" => date("d. M Y - H:i:s", $this->timestamp),
                "totalBuy" => $this->totalBuy,
                "totalSell" => $this->totalSell,
                "totalDeposit" => $this->totalDeposit,
                "sum" => $this->totalSell + $this->totalDeposit
            ];
        }

        /**
         * @return int
         */
        public function getRID() {
            return $this->rID;
        }

        /**
         * @param int $rID
         */
        public function setRID($rID) {
            $this->rID = $rID;
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
         * @return int
         */
        public function getTimestamp() {
            return $this->timestamp;
        }

        /**
         * @param int $timestamp
         */
        public function setTimestamp($timestamp) {
            $this->timestamp = $timestamp;
        }

        /**
         * @return float
         */
        public function getTotalBuy() {
            return $this->totalBuy;
        }

        /**
         * @param float $totalBuy
         */
        public function setTotalBuy($totalBuy) {
            $this->totalBuy = $totalBuy;
        }

        /**
         * @return int
         */
        public function getTotalSell() {
            return $this->totalSell;
        }

        /**
         * @param int $totalSell
         */
        public function setTotalSell($totalSell) {
            $this->totalSell = $totalSell;
        }

        /**
         * @return int
         */
        public function getTotalDeposit() {
            return $this->totalDeposit;
        }

        /**
         * @param int $totalDeposit
         */
        public function setTotalDeposit($totalDeposit) {
            $this->totalDeposit = $totalDeposit;
        }
    }