<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:21
     */

    require_once "../../classes/Item.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $iID = intval($_GET["iID"]);

    $item = \POS\Item::fromIID($iID);

    if(isset($_POST["barcode"]))  $item->setBarcode  ($_POST["barcode"]);
    if(isset($_POST["itemName"])) $item->setItemName ($_POST["itemName"]);
    if(isset($_POST["priceBuy"])) $item->setPriceBuy (intval($_POST["priceBuy"]));
    if(isset($_POST["priceSell"]))$item->setPriceSell(intval($_POST["priceSell"]));
    if(isset($_POST["inStock"]))  $item->setInStock  (intval($_POST["inStock"]));

    $item->saveChanges();
    echo json_encode(["success" => true]);
