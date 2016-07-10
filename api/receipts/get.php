<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 10.07.2016
     * Time: 23:54
     */

    require_once "../../classes/Receipt.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $rID = intval($_GET["rID"]);
    $receipt = \POS\Receipt::fromRID($rID);

    if($receipt->getRID() != null) {
        $array["receipt"] = $receipt->asArray();
        $array["success"] = true;
        echo json_encode($array);
    } else {
        echo json_encode(["success" => false, "errorCode" => 2]);
    }