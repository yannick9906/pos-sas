<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 10.07.2016
     * Time: 23:48
     */
    
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Receipt.php";
    require_once "../../classes/Item.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $rID = intval($_GET["rID"]);
    $barcode = $_GET["barcode"];

    $receipt = \POS\Receipt::fromRID($rID);
    $item = \POS\Item::fromBarcode($barcode);

    if($receipt->getRID() != null && $item->getIID() != null) {
        $receipt->addItem($item);
        $toEncode["success"] = true;
    } else {
        $toEncode["success"] = false;
        $toEncode["errorCode"] = 2;
    }
    echo json_encode($toEncode);