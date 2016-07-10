<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 10.07.2016
     * Time: 22:14
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Receipt.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $cID = $_POST["cID"];

    $toEncode["receipt"] = \POS\Receipt::createNew($cID)->asArray();
    $toEncode["success"] = true;
    echo json_encode($toEncode);