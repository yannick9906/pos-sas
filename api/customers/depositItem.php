<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 11.07.2016
     * Time: 23:22
     */

    require_once "../../classes/Customer.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Item.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $cID = intval($_GET["cID"]);
    $barcode = $_GET["barcode"];

    $customer = \POS\Customer::fromCID($cID);

    if($customer->getCID() != null && $barcode != "") {
        $toEncode["success"] = $customer->depositItem($barcode);
    } else {
        $toEncode["success"] = false;
        $toEncode["errorCode"] = 2;
    }
    echo json_encode($toEncode);