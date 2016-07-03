<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 03.07.2016
     * Time: 02:32
     */

    require_once "../../classes/User.php";
    require_once "../../classes/PDO_Mysql.php";

    $action = $_GET["action"];

    if($action == "login") {
        $jsonarray = ["success" => 0];
        $username  = $_POST["usrname"];
        $passwd    = $_POST["passwd"];

        if(!\POS\User::doesUserNameExist($username)) {
            $jsonarray["errorcode"] = 4;
            $jsonarray["errormsg"] = "Der Benutzername existiert nicht";
        } elseif($username != null and $passwd != null) {
            $user = \POS\User::fromUName($username);
            if($user->comparePassHash($passwd)) {
                $jsonarray["success"] = 1;
                session_start();
                $_SESSION["uID"] = $user->getUID();
                $jsonarray["forwardTo"] = "appUser.html";
            } else {
                $jsonarray["errorcode"] = 3;
                $jsonarray["errormsg"] = "Kennwort falsch";
            }
        } else {
            $jsonarray["errorcode"] = 2;
            $jsonarray["errormsg"] = "Das Formular muss komplett ausgefüllt sein";
        }
        echo json_encode($jsonarray);
    } elseif($action == "logout") {
        session_start();
        session_destroy();
        echo json_encode(["success" => true]);
    }