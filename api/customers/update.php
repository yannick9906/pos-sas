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

    if(isset($_POST["name"]))    $customer->setName   ($_POST["name"]);
    if(isset($_POST["barcode"])) $customer->setBarcode($_POST["barcode"]);

    $customer->saveChanges();
    echo json_encode(["success" => true]);
