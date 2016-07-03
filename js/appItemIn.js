/**
 * Created by yanni on 03.07.2016.
 */
var scanning = false;
function doScan() {
    $("#interactive").show();
    $("#iteminfo").hide();
    $(function () {
        var App = {
            init: function () {
                Quagga.init(this.state, function (err) {
                    if (err) {
                        console.log(err);
                        return;
                    }
                    Quagga.start();
                });
            },
            state: {
                inputStream: {
                    type: "LiveStream",
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment" // or user
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 4,
                decoder: {
                    readers: ["ean_reader"]
                },
                locate: true
            },
            lastResult: null
        };

        App.init();

        Quagga.onProcessed(function (result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }

                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }

                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });

        Quagga.onDetected(function (result) {
            var code = result.codeResult.code;
            console.log(code);

            if (App.lastResult !== code) {
                if(!scanning) {
                    scanning = true;
                    $.getJSON("api/items/get.php?barcode=" + code, null, function (json) {
                        if (json["success"] == true) {
                            $("#i_itemName").html(json.itemName);
                            $("#i_priceBuy").html(json.priceBuy);
                            $("#i_priceSell").html(json.priceSell);
                            $("#i_inStock").html(json.inStock);

                            post = {
                                inStock: parseInt(json.inStock) + 1
                            }
                            $.post("api/items/update.php?iID=" + json.iID, post, function(data) {
                                data = JSON.parse(data);
                                if (data["success"] == true) {
                                    Materialize.toast("Eingelagert", 2000, "green");
                                    Quagga.stop();
                                    $("#interactive").hide();
                                    $("#itemInfo").show();

                                } else {
                                    if (data["error"] == "NoLogin") window.location.href = "appLogin.html";
                                    else Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
                                }
                                scanning = false;
                            });
                            Quagga.stop();
                        } else if (json["errorCode"] == 1) {
                            Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
                            scanning = false;
                        } else if (json["errorCode"] == 2) {
                            Materialize.toast('Dieser Artikel existiert nicht', 100, 'red');
                            scanning = false;
                        } else {
                            if (json["error"] == "NoLogin") window.location.href = "appLogin.html";
                            else Materialize.toast('Es ist ein Fehler aufgetreten. Das tut uns leid :/', 2000, 'red');
                            scanning = false;
                        }
                    });
                }
            }
        });
    });
}