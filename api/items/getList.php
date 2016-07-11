<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:11
     */

    require_once "../../classes/Item.php";
    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    //$user = \POS\Util::checkSession();

    $page = intval($_GET["page"]);
    $pagesize = 75;
    $search = $_GET["search"];

    $itemObj = \POS\Item::getListObjects($page, $pagesize, $search);
    $meta = \POS\Item::getListMeta($page, $pagesize, $search);
    $items = [];
    foreach ($itemObj as $item) {
        array_push($items, $item->asArray());
    }
    $meta["items"] = $items;
    echo json_encode($meta);