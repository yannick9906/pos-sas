<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 03.07.2016
     * Time: 02:22
     */

    require_once "../../classes/Customer.php";
    require_once "../../classes/PDO_Mysql.php";
    
    echo json_encode(["exist" => \POS\Customer::doesCustomerExist($_GET["barcode"])]);