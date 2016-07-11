<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 11.07.2016
     * Time: 01:17
     */

    require_once "../../classes/Customer.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Item.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $cID = intval($_GET["cID"]);

    $customer = \POS\Customer::fromCID($cID);

    if($customer->getCID() != null) {
        $items = $customer->getDepositItems();
        $toEncode["items"] = $items;
        $toEncode["success"] = true;
    } else {
        $toEncode["success"] = false;
        $toEncode["errorCode"] = 2;
    }
    echo json_encode($toEncode);
