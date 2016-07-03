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
    //$user = \POS\Util::checkSession();

    $iID = intval($_GET["iID"]);
    $item = \POS\Item::fromIID($iID);

    echo json_encode($item->asArray());