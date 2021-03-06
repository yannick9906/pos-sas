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

    $receipt = \POS\Receipt::fromRID($rID);

    if($receipt->getRID() != null) {
        $items = $receipt->getItems();
        $toEncode["items"] = $items;
        $toEncode["success"] = $receipt->getRID() != null;
    } else {
        $toEncode["success"] = false;
        $toEncode["errorCode"] = 2;
    }
    echo json_encode($toEncode);
