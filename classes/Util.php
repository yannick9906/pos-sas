<?php
/**
 * Created by PhpStorm.
 * User: yannick
 * Date: 06.03.2016
 * Time: 18:17
 *
 * Hier kommt alles an schönen Funktionen rein, die nicht in eine Klasse passen
 * Alle Funktionen bitte static machen
 */

namespace POS;
use DateTime;
use DateTimeZone;
use POS\User;

class Util {
    /**
     * @return bool|\POS\User
     */
    public static function checkSession() {
        session_start();
        if(!isset($_SESSION["uID"])) {
            echo json_encode(["success" => false, "error" => "NoLogin"]);
            exit;
        } else {
            $user = User::fromUID($_SESSION["uID"]);
            if($_GET["m"] == "debug") {
                echo "<pre style='display: block; position: absolute'>\n";
                echo "[0] Perm Array Information:\n";
                echo "Not available on this platform";
                echo "\n[1] Permission Information:\n";
                echo "Not available on this platform";
                echo "\n[2] User Information:\n";
                echo $user->toString();
                echo "\n[3] Client Information:\n";
                echo "    Arguments: ".$_SERVER["REQUEST_URI"]."\n";
                echo "    Req Time : ".$_SERVER["REQUEST_TIME"]."ns\n";
                echo "    Remote IP: ".$_SERVER["REMOTE_ADDR"]."\n";
                echo "    Usr Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n";
                echo "</pre>\n";
            }
            return $user;
        }
    }

    /**
     * Forwards the user to a specific url
     *
     * @param $url
     */
    public static function forwardTo($url) {
        echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\" />";
    }

    /**
     * @param $title String
     * @param $user \POS\User
     * @param bool $backable
     * @param bool $editor
     * @param string $undoUrl
     * @return array
     */
    public static function getEditorPageDataStub($title, $user, $backable = false, $editor = false, $undoUrl = "") {
        return [
            "header" => [
                "title" => $title,
                "usrname" => $user->getUName(),
                "realname" => $user->getURealname(),
                "editor" => $editor ? 1:0,
                "undoUrl" => $undoUrl,
                "backable" => $backable ? 1:0
            ],
        ];
    }

    /**
     * @param $secs int
     * @return string
     */
    public static function seconds_to_time($secs) {
        $dt = new DateTime('@' . $secs, new DateTimeZone('UTC'));
        $array = array('days'    => $dt->format('z'),
                       'hours'   => $dt->format('G'),
                       'minutes' => $dt->format('i'),
                       'seconds' => $dt->format('s'));
        return $array["days"]." Tage ".$array["hours"]."h ".$array["minutes"]."m ".$array["seconds"]."s";
    }

    public static function redGreenNegPos($value) {
        if($value > 0) return "<span class='green-text'>+$value S</span>";
        if($value == 0) return "<span class='black-text'>$value S</span>";
        else return "<span class='red-text'>$value S</span>";
    }
}