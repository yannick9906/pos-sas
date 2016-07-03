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