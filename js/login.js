/**
 * Created by yanni on 28.04.2016.
 */
function doLogout() {
    $.getJSON("userLogin.php?action=logout", null, function(data) {
        if(data["success"]) {
            Materialize.toast("Logout erfolgreich", 1000, "green");
        } else {
            Materialize.toast("Logout fehlgeschlagen", 5000, "red");
        }
    });
}

function login() {
    var usrname  = $("#l_usrname").val();
    var passwd1  = $("#l_passwd").val();

    var passwd = md5(passwd1);
    data = {
        usrname: usrname,
        passwd: passwd
    };
    $.post("userLogin.php?action=login", data, function(data) {
        data = JSON.parse(data);
        var field = $("#usrname");
        if(data["success"] == 1) {
            Materialize.toast('Login erfolgreich', 2000, 'green');
            window.location.href = data["forwardTo"];
        } else if(data["errorcode"] == 4) {
            Materialize.toast('Der Benutzername existiert nicht.', 4000, 'red');
        } else if(data["errorcode"] == 3) {
            Materialize.toast('Passwort falsch', 4000, 'red');
        } else if(data["errorcode"] == 5) {
            Materialize.toast('Der Account ist noch nicht freigeschalten.', 4000, 'red');
        } else {
            Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 4000, 'red');
        }

    });
}

function checkUsrname() {
    var field = $("#usrname");
    $.getJSON("userLogin.php?action=validateUsername&username="+field.val(), null, function(data) {
        if(field.val() == "") {
            field.removeClass("invalid");
            field.removeClass("valid");
        } else if(data["success"] == 1) {
            field.removeClass("invalid");
            field.addClass("valid");
        } else {
            field.addClass("invalid");
            field.removeClass("valid");
        }
    });
}