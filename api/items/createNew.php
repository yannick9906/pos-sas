<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:20
     */

    require_once "../../classes/Item.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    //$user = \POS\Util::checkSession();

    $itemName  = $_POST["itemName"];
    $priceBuy  = $_POST["priceBuy"];
    $priceSell = $_POST["priceSell"];
    $barcode   = $_POST["barcode"];

    \POS\Item::createNew($itemName, 0, $priceBuy, $priceSell, $barcode);
    echo json_encode(["success" => true]);