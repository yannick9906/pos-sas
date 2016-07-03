<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:21
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $uID = intval($_GET["uID"]);

    $user = \POS\User::fromUID($uID);
    $user->delete();

    echo json_encode(["success" => true]);