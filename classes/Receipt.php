<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 30.06.2016
     * Time: 21:19
     */

    namespace POS;


    class Receipt {
        private $pdo, $rID, $cID, $timestamp, $totalBuy, $totalSell;

        /**
        * Receipt constructor.
        *
        * @param $rID
        * @param $cID
        * @param $timestamp
        * @param $totalBuy
        * @param $totalSell
        */
        public function __construct($rID, $cID, $timestamp, $totalBuy, $totalSell) {
            $this->rID = $rID;
            $this->cID = $cID;
            $this->timestamp = $timestamp;
            $this->totalBuy = $totalBuy;
            $this->totalSell = $totalSell;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new Receipt Object from a give receipt ID
         *
         * @param $rID int Receipt ID
         * @return User
         */
        public static function fromRID($rID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM pos_receipt WHERE rID = :rid", [":rid" => $rID]);
            return new Receipt($res->rID, $res->cID, $res->timestamp, $res->totalBuy, $res->totalSell);
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
    }