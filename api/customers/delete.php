<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:21
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    require_once "../../classes/Customer.php";
    $user = \POS\Util::checkSession();

    $cID = intval($_GET["cID"]);

    $customer = \POS\Customer::fromCID($cID);
    $customer->delete();

    echo json_encode(["success" => true]);