<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:20
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Customer.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    if(isset($_POST["name"])) $name = $_POST["name"]; else $name = "";
    $barcode = str_replace("a", "", $_POST["barcode"]);

    \POS\Customer::createNew($name, $barcode);
    echo json_encode(["success" => true]);