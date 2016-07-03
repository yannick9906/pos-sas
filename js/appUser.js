/**
 * Created by yanni on 03.07.2016.
 */

var userID = 0;

function loadData() {
    $.getJSON("api/users/currentUser.php", null, function(data) {
        userID = data.uID;
        $("#usrname").val(data.username);
        $("#realname").val(data.realname);
        Materialize.updateTextFields();
    });
}

function submit() {
    var passwd1  = $("#passwd1").val();
    var passwd2  = $("#passwd2").val();

    if(passwd1 == passwd2) {
        var passwd = md5(passwd1);
        data = {
            passwd: passwd
        };
        $.post("api/users/update.php?uID="+userID, data, function(data) {
            data = JSON.parse(data);
            if(data["success"] == true) {
                Materialize.toast("Gespeichert", 2000, "green");
            } else if(data["errorcode"] == 2) Materialize.toast('Es müssen alle Felder ausgefüllt sein', 2000, 'red');
            else {
                if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
                else Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
            }
        });
    } else {
        Materialize.toast('Die Passwörter stimmen nicht überein', 2000, 'red');
    }
}