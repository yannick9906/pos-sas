<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 11.07.2016
     * Time: 01:17
     */

    require_once "../../classes/Item.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();
    
    $barcode = $_GET["barcode"];
    $item = \POS\Item::fromBarcode($barcode);

    if($item->getIID() != null) {
        $array = $item->
        $array["success"] = true;
        echo json_encode($array);
    } else {
        echo json_encode(["success" => false, "errorCode" => 2]);
    }