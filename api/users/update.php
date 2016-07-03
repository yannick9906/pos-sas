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

    $userEdit = \POS\User::fromUID($uID);

    if(isset($_POST["username"])) $userEdit->setUName    ($_POST["username"]);
    if(isset($_POST["realname"])) $userEdit->setURealname($_POST["realname"]);
    if(isset($_POST["passwd"]))   $userEdit->setUPassHash($_POST["passwd"]);

    $userEdit->saveChanges();
    echo json_encode(["success" => true]);
