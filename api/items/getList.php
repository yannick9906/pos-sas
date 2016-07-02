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

    $items = \POS\Item::getList($page, $pagesize, $search);
    echo json_encode($items);