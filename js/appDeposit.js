/**
 * Created by yanni on 11.07.2016.
 */
var currCustomer = -1;
var listTmplt = Handlebars.compile(`
    <tr id="entry{{iID}}">
        <td>{{itemName}}</td>
        <td>{{amount}}x</td>
        <td>{{priceDeposit}} S</td>
    </tr>
    `);

$(document).ready(function() {
    $("#barcode").on("keydown", function(e) {
        console.log(e.which);
        if(e.which == 13) {
            e.preventDefault();
            if(currCustomer == -1) checkCustomer();
            else depositItem();
        }

    });
})

function checkCustomer() {
    var barcode = $("#barcode").val();
    $.getJSON("api/customers/checkCustomer.php?barcode="+barcode, null, function(data) {
        if(data.error == "NoLogin") window.location.href = "appLogin.html";
        else if(!data.exist) {
            Materialize.toast("Kunde unbekannt", 500, "red");
        } else {
            Materialize.toast("Kunde bekannt", 500, "green");
            $.getJSON("api/customers/get.php?barcode="+barcode, null, function(data) {
                if(data.error == "NoLogin") window.location.href = "appLogin.html";
                else {
                    currCustomer = data.cID;
                    $("#customer_id").html(data.cID);
                    $("#customer_name").html(data.name);
                    $("#customer_barcode").html(data.barcode);
                    $("#customer_value").html(0 + " S");
                    $("#customer_depositLeft").html(0 + " S");
                    updateList();
                }
            });
        }
    });
    $("#barcode").val("");
    $("#barcode").focus();
}

function depositItem() {
    var barcode = $("#barcode").val();
    $.getJSON("api/customers/depositItem.php?barcode="+barcode+"&cID="+currCustomer, null, function(data) {
        if(data.error == "NoLogin") window.location.href = "appLogin.html";
        else if(data.success) Materialize.toast("Pfand ausgezahlt", 500, "green");
        else Materialize.toast("Ein Fehler ist aufgetreten", 500, "red");
        updateList();
    });
    $("#barcode").val("");
}

function updateList() {
    $.getJSON("api/customers/getDepositItems.php?cID="+currCustomer,null, function(data) {
        $("#items").html("");
        if(data.error == "NoLogin") window.location.href = "appLogin.html";
        else {
            data["items"].forEach(function (element, index, array) {
                if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
                else {
                    html = listTmplt(element);
                    $("#items").append(html);
                }
            });
        }
    });
}

function finish() {
    Materialize.toast("Abgeschlossen.", 2000, "green");
    currCustomer = -1;

    $("#customer_id").html("0");
    $("#customer_name").html(".");
    $("#customer_barcode").html(".");
    $("#customer_value").html(0 + " S");
    $("#customer_depositLeft").html(0 + " S");

    $("#items").html("<tr><td colspan='3'>Warte auf Scan...</td></tr>")
}