<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 03.07.2016
     * Time: 16:04
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $json = json_encode($user->asArray());

    echo $json;