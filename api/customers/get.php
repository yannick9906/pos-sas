<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 10.07.2016
     * Time: 18:32
     */

    require_once "../../classes/Customer.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Item.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    if(isset($_GET["cID"])) {
        $cID = intval($_GET["cID"]);
        $customer = \POS\Customer::fromCID($cID);

        if($customer->getCID() != null) {
            $array = $customer->asArray();
            $array["success"] = true;
            echo json_encode($array);
        } else {
            echo json_encode(["success" => false, "errorCode" => 2]);
        }
    } elseif(isset($_GET["barcode"])) {
        $barcode = $_GET["barcode"];
        $customer = \POS\Customer::fromBarcode($barcode);

        if($customer->getCID() != null) {
            $array = $customer->asArray();
            $array["success"] = true;
            echo json_encode($array);
        } else {
            var_dump($customer);
            echo json_encode(["success" => false, "errorCode" => 2]);
        }
    } else {
        echo json_encode(["success" => false, "errorCode" => 1]);
    }