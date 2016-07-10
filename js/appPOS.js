/**
 * Created by yanni on 10.07.2016.
 */

var listTmplt = Handlebars.compile(`
    <tr id="entry{{iID}}">
        <td>{{amount}}x</td>
        <td>{{itemName}}</td>
        <td>{{priceSell}} S</td>
    </tr>
    `);
var currReceipt = -1;

$(document).ready(function() {
    $("#barcode").on("keydown", function(e) {
        console.log(e.which);
        if(e.which == 13) {
            e.preventDefault();
            if(currReceipt == -1) checkCustomer();
            else addItem();
        }

    });
})

function checkCustomer() {
    var barcode = $("#barcode").val();
    $.getJSON("api/customers/checkCustomer.php?barcode="+barcode, null, function(data) {
        if(data.error == "NoLogin") window.location.href = "appLogin.html";
        else if(!data.exist) {
            Materialize.toast("Neuer Kunde", 500, "green");
            $.post("api/customers/createNew.php", {barcode: barcode}, function(data) {
                data = JSON.parse(data);
                if(data.error == "NoLogin") window.location.href = "appLogin.html";
                else if(data.success) Materialize.toast("Kunde erstellt.", 500, "green");
                else Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
            });
        }
        $.getJSON("api/customers/get.php?barcode="+barcode, null, function(data) {
            if(data.error == "NoLogin") window.location.href = "appLogin.html";
            else {
                $("#customer_id").html(data.cID);
                $("#customer_name").html(data.name);
                $("#customer_barcode").html(data.barcode);
                $("#customer_value").html(0 + " S");
                $("#customer_depositLeft").html(0 + " S");
                $.post("api/receipts/createNew.php", {cID: data.cID}, function(data) {
                    data = JSON.parse(data);

                    if(data.error == "NoLogin") window.location.href = "appLogin.html";
                    else if(data.success) {
                        Materialize.toast("Einkauf erstellt.", 500, "green");
                        currReceipt = data.receipt.rID;
                        $("#receipt_id").html(data.receipt.rID);
                        $("#receipt_timestamp").html(data.receipt.timestamp);
                        $("#receipt_value").html(data.receipt.totalSell + " S");
                        $("#receipt_deposit").html(data.receipt.totalDeposit + " S");
                        $("#receipt_sum").html(data.receipt.sum + " S");
                    }
                    else Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
                })
            }
        })
    });
    $("#barcode").val("");
}

function addItem() {
    var barcode = $("#barcode").val();
    $.getJSON("api/receipts/addItem.php?barcode="+barcode+"&rID="+currReceipt, null, function(data) {
        if(data.error == "NoLogin") window.location.href = "appLogin.html";
        else if(data.success) Materialize.toast("Artikel hinzugefügt.");
        else Materialize.toast("Artikel existiert nicht.");
        updateDetails();
        updateList();
    });
    $("#barcode").val("");
}

function updateList() {
    $.getJSON("api/receipts/getItems.php?rID="+currReceipt,null, function(data) {
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
    })
}

function updateDetails() {
    $.getJSON("api/receipts/get.php?rID=" + currReceipt, null, function (data) {
        if (data.error == "NoLogin") window.location.href = "appLogin.html";
        else {
            $("#receipt_id").html(data.receipt.rID);
            $("#receipt_timestamp").html(data.receipt.timestamp);
            $("#receipt_value").html(data.receipt.totalSell + " S");
            $("#receipt_deposit").html(data.receipt.totalDeposit + " S");
            $("#receipt_sum").html(data.receipt.sum + " S");
        }
    });
}

function finish() {
    currReceipt = -1;
    $("#receipt_id").html(0);
    $("#receipt_value").html("0 S");
    $("#receipt_deposit").html("0 S");
    $("#receipt_sum").html("0 S");

    $("#customer_id").html("0");
    $("#customer_name").html(".");
    $("#customer_barcode").html(".");
    $("#customer_value").html(0 + " S");
    $("#customer_depositLeft").html(0 + " S");

    $("#items").html("<tr><td colspan='3'>Warte auf Scan...</td></tr>")
}