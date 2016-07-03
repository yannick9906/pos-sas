<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 03.07.2016
     * Time: 17:07
     */

    require_once "../../classes/Item.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    if(isset($_GET["iID"])) {
        $iID = intval($_GET["iID"]);
        $item = \POS\Item::fromIID($iID);

        if($item->getIID() != null) {
            $array = $item->asArray();
            $array["success"] = true;
            echo json_encode($array);
        } else {
            echo json_encode(["success" => false, "errorCode" => 2]);
        }
    } elseif(isset($_GET["barcode"])) {
        $barcode = intval($_GET["barcode"]);
        $item = \POS\Item::fromBarcode($barcode);

        if($item->getIID() != null) {
            $array = $item->asArray();
            $array["success"] = true;
            echo json_encode($array);
        } else {
            echo json_encode(["success" => false, "errorCode" => 2]);
        }
    } else {
        echo json_encode(["success" => false, "errorCode" => 1]);
    }