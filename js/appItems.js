/**
 * Created by yanni on 03.07.2016.
 */

var oldData = "";
var listTmplt = Handlebars.compile(`
    <tr class="{{color}} clickable" id="entry{{iID}}">
        <td onclick="toDetail({{iID}})">{{iID}}</td>
        <td onclick="toDetail({{iID}})">{{itemName}}</td>
        <td onclick="toDetail({{iID}})">{{priceBuy}} S</td>
        <td onclick="toDetail({{iID}})">{{priceSell}} S</td>
        <td onclick="toDetail({{iID}})">{{inStock}} Stk.</td>
    </tr>
    `);
var searchString = "";
var currPage = 1;
var reqPage = 1;
var maxPages;
var autoUpdate = true;

function update() {
    $.getJSON("api/items/getList.php?search="+searchString+"&page="+reqPage,null,function(data) {
        if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
        else if(!(JSON.stringify(oldData) == JSON.stringify(data))) {
            $("#loading").fadeIn(100);
            $("#items").html("");
            data["items"].forEach(function (element, index, array) {
                if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
                else {
                    html = listTmplt(element);
                    $("#items").append(html);
                }
            });
            oldData = data;
        }
        $("#loading").fadeOut(100);
        currPage = data["page"];
        maxPages = data["maxPage"];
    });
}

function setPage(apage) {
    reqPage = apage;
    doUpdate();
}

function updatePages() {
    if(currPage > maxPages) {
        requestPage = maxPages;
        if(reqPage == 0) reqPage = 1;
    }
    nextPage = parseInt(currPage)+1;
    prevPage = currPage-1;
    p = $("#pages");
    p.html("");
    p.append("<div id='pagesPre' class='col s1'></div>");
    p.append("<div id='pagesSuf' class='col push-s10 s1'></div>");
    p.append("<div id='pagesNum' class='col pull-s1 s10'></div>");

    if(currPage <= 1) $("#pagesPre").append("<li class=\"disabled\"><a><i class=\"mddi mddi-chevron-left\"></i></a></li>");
    else $("#pagesPre").append("<li class=\"waves-effect\"><a onclick=\"setPage("+prevPage+")\"><i class=\"mddi mddi-chevron-left\"></i></a></li>");

    for(i = 1; i <= maxPages; i++) {
        if(i != currPage) {
            $("#pagesNum").append("<li class=\"waves-effect\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        } else {
            $("#pagesNum").append("<li class=\"active green\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        }
    }

    if(currPage >= maxPages) $("#pagesSuf").append("<li class=\"disabled\"><a><i class=\"mddi mddi-chevron-right\"></i></a></li>");
    else $("#pagesSuf").append("<li class=\"waves-effect\"><a onclick=\"setPage("+nextPage+")\"><i class=\"mddi mddi-chevron-right\"></i></a></li>");
}


function updateSchedueler() {
    if(autoUpdate == true) {
        update();
        updatePages();
    }
    window.setTimeout("updateSchedueler()", 1000);
}

function toList() {
    $("#menu-back").fadeOut();
    $("#menu-back-d").fadeOut();
    $("#menu-norm").fadeIn();
    $("#newItemBtn").fadeIn();
    $("#newItem").fadeOut(200);
    $("#detailItem").fadeOut(200, function() {
        $("#itemList").fadeIn(200);
    });
    autoUpdate = true;
    update();
}

///////////////////////////////////////////////////////
var currItem = 0

function toDetail(iID) {
    currItem = iID;
    $("#menu-back").fadeIn();
    $("#menu-back-d").fadeIn();
    $("#menu-norm").fadeOut();
    $("#newItemBtn").fadeOut();
    $("#newItem").fadeOut(200);
    $("#itemList").fadeOut(200, function() {
        $("#detailItem").fadeIn(200);
    });
    autoUpdate = false;
    loadData();
}

function loadData() {
    $.getJSON("api/items/get.php?iID="+currItem, null, function(item) {
        if(item["error"] == "NoLogin") window.location.href = "appLogin.html";
        else {
            $("#iID").val(item.iID);
            $("#itemName").val(item.itemName);
            $("#priceBuy").val(item.priceBuy);
            $("#priceSell").val(item.priceSell);
            $("#inStock").val(item.inStock);
            Materialize.updateTextFields();
        }
    });
}

function updateItem() {
    var itemname  = $("#itemName").val();
    var priceBuy  = $("#priceBuy").val();
    var priceSell = $("#priceSell").val();
    var inStock   = $("#inStock").val();

    if(itemname != "" && priceBuy != "" && priceSell != "" && inStock != "") {
        data = {
            itemname: itemname,
            priceBuy: priceBuy,
            priceSell: priceSell,
            inStock: inStock
        };
        $.post("api/items/update.php?iID="+currItem, data, function(data) {
            data = JSON.parse(data);
            if(data["success"] == true) {
                Materialize.toast("Gespeichert", 2000, "green");
            } else {
                if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
                else Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
            }
        });
    } else {
        Materialize.toast('Es müssen alle Felder ausgefüllt werden', 2000, 'red');
    }
}

///////////////////////////////////////////////////////

function toNewItem() {
    $("#menu-back").fadeIn();
    $("#menu-back-d").fadeIn();
    $("#menu-norm").fadeOut();
    $("#newItemBtn").fadeOut();
    $("#detailItem").fadeOut(200);
    $("#itemList").fadeOut(200, function() {
        $("#newItem").fadeIn(200);
    });
    autoUpdate = false;
}

function createItem() {
    var itemname  = $("#n_itemName").val();
    var priceBuy  = $("#n_priceBuy").val();
    var priceSell = $("#n_priceSell").val();
    var inStock   = $("#n_inStock").val();
    var barcode   = $("#n_barcode").val();

    if(itemname != "" && priceBuy != "" && priceSell != "" && inStock != "" && barcode != "") {
        data = {
            itemName: itemname,
            priceBuy: priceBuy,
            priceSell: priceSell,
            inStock: inStock,
            barcode: barcode
        };
        $.post("api/items/createNew.php?", data, function(data) {
            data = JSON.parse(data);
            if(data["success"] == true) {
                Materialize.toast("Gespeichert", 2000, "green");
                toList();
            } else {
                if(data["error"] == "NoLogin") window.location.href = "appLogin.html";
                else Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
            }
        });
    } else {
        Materialize.toast('Es müssen alle Felder ausgefüllt werden', 2000, 'red');
    }
}