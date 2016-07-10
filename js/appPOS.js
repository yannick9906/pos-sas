/**
 * Created by yanni on 10.07.2016.
 */

currReceipt = -1;

$(document).ready(function() {
    $("#barcode").on("keydown", function(e) {
        if(e.which == 13) {
            if(currReceipt == -1) checkCustomer();
            else addItem();
            e.preventDefault();
        }
        console.log(e.which);

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
                        $("#receipt_value").html(data.receipt.totalBuy + " S");
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