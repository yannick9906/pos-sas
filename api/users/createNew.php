<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 02.07.2016
     * Time: 16:20
     */

    require_once "../../classes/PDO_Mysql.php";
    require_once "../../classes/User.php";
    require_once "../../classes/Util.php";
    $user = \POS\Util::checkSession();

    $username  = $_POST["username"];
    $realname  = $_POST["realname"];
    $passhash  = md5($_POST["passwd"]);

    \POS\User::createUser($username, $realname, $passhash);
    echo json_encode(["success" => true]);