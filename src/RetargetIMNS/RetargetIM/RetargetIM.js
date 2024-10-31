var userCookie;
var wpCookies = "wp_woocommerce_session";

var prodIDS = [];
var isAlertifyLoaded = false;
var FCM;
var Token;
var server;
var config;
var InvitationTextBody = "Click here to get peronnal notifications just for you.";
var folderPath = 'https://' + document.location.host + '/wp-content/plugins/retargetim/src/RetargetIMNS/RetargetIM';

//NEW FUNC ! VIEWED


function ItemViewed(prodID) {
    var http = new XMLHttpRequest();
    var url = server + '/ItemViewed';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}


function ItemTime(prodID, time) {
    var http = new XMLHttpRequest();
    var url = server + '/ItemTime';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc,
                    "time": time
                }
            params += uc; //will be removed
            params += "&time=" + time;

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function ItemPicClick(prodID) {
    var http = new XMLHttpRequest();
    var url = server + '/ItemPic';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}


// remove
function removeItem(prodID) {
    var http = new XMLHttpRequest();
    var url = server + '/RemoveFromCart';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            params += "&RIM=" + isRim;
            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc,
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function checkOut(isRim) {
    var http = new XMLHttpRequest();
    var url = server + '/checkOut';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            params += "&RIM=" + isRim;
            var jsonParams =
                {
                    "fcmToken": Token,
                    "session": uc,
                    "RIM": isRim
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function placeOrder(isRim) {
    var http = new XMLHttpRequest();
    var url = server + '/placeOrder';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {


            params += "&RIM=" + isRim;
            var jsonParams =
                {
                    "fcmToken": Token,
                    "session": uc,
                    "RIM": isRim
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function UpsertCust() {
    var http = new XMLHttpRequest();
    var url = server + '/UpsertCust';
    var params = "session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {
            var jsonParams =
                {
                    "fcmToken": Token,
                    "session": uc
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function SendToServer(prodID) {
    var http = new XMLHttpRequest();
    var url = server + '/AddToCart';

    var params = "items=" + prodID + "&fcmToken=" + Token + "&session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {

            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });

        });
    }
}


function OpenedNotif(prodID) {
    var http = new XMLHttpRequest();
    var url = server + '/OpenedNotif';
    if (prodID == null)
        prodID = 0;
    var params = "items=" + prodID + "&session=";
    if (document.cookie.includes("RetargetIM")) {
        getRetargetIMCookieCB(function (uc) {

            var jsonParams =
                {
                    "items": prodID,
                    "fcmToken": Token,
                    "session": uc
                }
            params += uc; //will be removed

            if (document.cookie.includes("woocommerce_cart_hash")) {
                getWPSessionCookieCB(function (wc_cookie) {
                    jsonParams['wcSession'] = wc_cookie;
                });
            }
            jQuery.ajax({
                method: 'POST',
                url: url,
                data: jsonParams,
                async: false
            });
        });
    }
}

function CustClicked() {
    /*
     var http = new XMLHttpRequest();
     var url = server + '/CustClicked';
     var params = "session=";
     if (document.cookie.includes("RetargetIM")) {
     getRetargetIMCookieCB(function (uc) {

     params += uc;

     if (document.cookie.includes("woocommerce_cart_hash")) {
     getWPSessionCookieCB(function (wc_cookie) {
     params += "&wcSession=" + wc_cookie;
     });
     }

     if (Token != null)
     params += "&fcmToken=" + Token;

     http.open("GET", url + "?" + params, true);

     http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
     http.onload = function (e) {
     if (http.readyState === 4) {
     if (http.status === 200) {
     console.log(http.responseText);
     } else {
     console.error(http.statusText);
     }
     }
     };
     http.onerror = function (e) {
     console.error(http.statusText);
     };

     http.send();
     });
     }
     */
}

function SendSerial(serial) {    //alert("Hello from func!");

    var http = new XMLHttpRequest();
    var url = server + '/Set_serial';
    getRetargetIMCookieCB(function (uc) {
        userCookie = uc;
        if (userCookie != null) {
            var params = "serial=" + serial + "&cookie=" + userCookie;

            //TODO: http.open("POST", url, true);
            http.open("GET", url + "?" + params, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //http.withCredentials = true;
            http.onload = function (e) {
                if (http.readyState === 4) {
                    if (http.status === 200) {
                        console.log(http.responseText);
                    } else {
                        console.error(http.statusText);
                    }
                }
            };
            http.onerror = function (e) {
                console.error(http.statusText);
            };
            //alert(url+"?"+params);
            http.send();
        }
    });
}

onload = function (e) {

    var timeonpage = new Date().getTime();
    //folderPath
    //alert(
    var arr = document.getElementsByTagName('script');
    for (var i = 0; i < arr.length; i++) {
        // if (arr[i].src.includes('RetargetIM.js'))
        // folderPath = arr[i].src.split('RetargetIM.js')[0];
    }

    loadConf(function () {
        loadListeners();
    });
}
function loadListeners() {
    {

        getRetargetIMCookieCB(function (uc) {
            userCookie = uc;
        });

    }

//general add to cart

    var prodList = document.getElementsByClassName("products");
    if (prodList.length > 0) {
        prodList = prodList[0].getElementsByTagName("li");
        for (var i = 0; i < prodList.length; i++) {
            //Product view
            var wcLoop = prodList[i].getElementsByClassName("woocommerce-LoopProduct-link");
            if (wcLoop == null || wcLoop.length == 0)
                wcLoop = prodList[i];
            else
                wcLoop = wcLoop[0];

            if (wcLoop != null) {
                wcLoop
                    .addEventListener("click", function (e) {
                        var getLIelem = e.target;
                        while (getLIelem.tagName != "LI" && getLIelem.tagName != "HTML") {
                            getLIelem = getLIelem.parentNode;
                        }
                        if (getLIelem.tagName == "LI") {
                            //"post-______ XYV"
                            //ItemViewed(getLIelem.className.substr(getLIelem.className.search('post-')).substring(5, 12).split(' ').shift());
                        }
                    });
            }
            //add-to-cart
            var addButton = prodList[i].getElementsByClassName("add_to_cart_button");
            if (addButton != null && addButton.length > 0) {
                addButton[0].addEventListener("click", function (e) {
                    SendToServer(e.target.getAttribute('data-product_id'))
                });
            }
        }
    }


    //add cart from the page
    var prodIDfromPage;
    var specProd = document.getElementsByClassName("single_add_to_cart_button button alt");
    if (specProd != null) {
        //if(specProd.length > 0)
        for (p = 0; p < specProd.length; p++) {
            if (specProd.length == 1 || document.location.href.toLowerCase().includes("/product/")) {
                //	specProd = specProd[p];
                //if(specProd.contains("input"))

                prodIDfromPage = specProd[p].value;


                var inputBrothersElem = specProd[p].parentElement.parentElement.getElementsByTagName("input");
                var inputIndex;
                if (inputBrothersElem != null) {

                    for (i = 0; i < inputBrothersElem.length; i++) {
                        if (inputBrothersElem[i].getAttribute("name") == "add-to-cart" && inputBrothersElem[i].getAttribute("value") != null) {
                            inputIndex = i;
                            if (prodIDfromPage == undefined || prodIDfromPage.length == 0) {
                                prodIDfromPage = inputBrothersElem[inputIndex].getAttribute("value");
                                break;
                            }
                        }
                    }
                }
                if (prodIDfromPage == null || prodIDfromPage == undefined || prodIDfromPage.length == 0) {
                    var getDivFather = specProd[p].parentElement;
                    while (getDivFather.tagName != 'div' && !getDivFather.id.startsWith('product-')) {
                        getDivFather = getDivFather.parentElement;
                    }
                    prodIDfromPage = getDivFather.id.substring(8);
                }
                var timestamp;
                window.onbeforeunload = function () {
                    ItemTime(prodIDfromPage, (new Date().getTime() - timeonpage) / 1000);
                }

                //After surely we have a code.
                ItemViewed(prodIDfromPage);
                function repeatedItemVisitTime() {
                    //  ItemVisitTime(	prodIDfromPage);
                    ItemTime(prodIDfromPage, 5);
                    setTimeout(repeatedItemVisitTime, 5000);
                }

                setTimeout(repeatedItemVisitTime, 5000);
                //

                var pic = document.getElementsByClassName("size-shop_single");
                if (pic.length > 0) {
                    pic[0].addEventListener("click", function (e) {
                        ItemPicClick(prodIDfromPage);
                    });
                }

                //specProd.
                //document.getElementsByClassName("single_add_to_cart_button button alt")[0].
                specProd[p].addEventListener("click", function () {
                    //	alert("clicked value:"+inputBrothersElem[inputIndex].getAttribute("value"));

                    SendToServer(prodIDfromPage);
                });

            }
        }
    }

//checkout-button
    //place order
    var checkoutButton = document.getElementsByClassName("checkout-button");
    if (checkoutButton != null && checkoutButton.length > 0) {
        checkoutButton = checkoutButton[0];

        if (window.location.href.toLowerCase().includes("retargetim")) //means we referrenced him
        {

            checkoutButton.addEventListener("click", function () {
                checkOut(true);
            });
        }
        else {
            checkoutButton.addEventListener("click", function () {
                checkOut(false)

            });
        }
    }

    var orderButton = document.getElementById("place_order");
    if (orderButton != null) {
        orderButton.addEventListener("click", function () {
            placeOrder();

        });
    }

    if (window.location.href.toLowerCase().includes("retargetim")) //means we referrenced him
        OpenedNotif(prodIDfromPage);

    addRemoveItemListener();
}
function addRemoveItemListener() {
    //removeProduct
    var prodTable = document.getElementsByClassName("shop_table shop_table_responsive cart");
    if (prodTable != null && prodTable.length > 0) {
        prodTable = prodTable[0];

        var removeBtns = prodTable.getElementsByClassName("product-remove");
        //1 and not 0 - because the first is TH
        for (var i = 1; i < removeBtns.length; i++) {
            removeBtns[i].getElementsByClassName("remove")[0].addEventListener("click", function (e) {

                removeItem(e.target.getAttribute("data-product_id"));
            });//.then(function(){addRemoveItemListener()});

        }
    }

}
if (!document.cookie.includes('RetargetIM')) {
    document.cookie = 'RetargetIM=' + guid();
}

function ValidateCartByFBID() {
    var params = window.location.href;
    if (params.contains("fbID")) //sent from BOT
    {
        var fbID = params.split("fbID").splice(1, 1)[0].substring(1, 17);
        alert('fbID: ' + fbID);

        getWPSessionCookieCB(function (WCC) {

            //to implement 2 funcs
            getCookieListByFbId(fbID, function (wcCookies) {
                getItemsByFbId(fbID, function (items) {
                    for (var i = 0; i < items.length; i++) {
                        SendToServer(items[i]);
                        var xhr = new XMLHttpRequest();
                        //alert(document.location.host + "/shop" + '?add-to-cart= ' + items[i]);
                        xhr.open('GET', document.location.host + "/shop" + '?add-to-cart= ' + items[i], true);
                        xhr.send();
                    }

                });
            });
        });
    }

}


function getCustDB(callback) {
    var CustFilePath = path.join(__dirname, '/../Data/Customers.json');

    fs.readFile(CustFilePath, 'utf8', function (err, data) {
        if (err) throw err; // we'll not consider error handling for now

        callback(JSON.parse(data));
    });
}

function getItemsByFbId(fbId, callback) {
    getCustDB(function (custJson) {
        var MatchCustArr = custJson.Customers.filter(function (cust) {
            return cust.fbId == fbID;
        });

        var itemArr;

        if (MatchCustArr.length > 0 && MatchCustArr[0].shopInfo.length > 0 && MatchCustArr[0].shopInfo[0].cart.items.length > 0) {
            itemArr = new Array(MatchCustArr[0].shopInfo[0].cart.items.length);
            for (var i = 0; i < MatchCustArr[0].shopInfo[0].cart.items.length; i++) {
                if (MatchCustArr[0].shopInfo[0] != null) {
                    cookieArr[i] = (MatchCustArr[0].shopInfo[0].cart.items[i].id);
                }
            }
            //alert(cookieArr);
            callback(cookieArr);
        }

    });

}

function getCookieListByFbId(fbID, callback) {
    getCustDB(function (custJson) {
        var MatchCustArr = custJson.Customers.filter(function (cust) {
            return cust.fbId == fbID;
        });

        var cookieArr = new Array(MatchCustArr.length);

        if (MatchCustArr.length > 0) {
            for (var i = 0; i < MatchCustArr[0].shopInfo.length; i++) {
                if (MatchCustArr[0].shopInfo[0] != null) {
                    cookieArr[i] = (MatchCustArr[0].shopInfo[i].session);
                }

            }
        }
        callback(cookieArr);
    });
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}


function addEventListenerOnce(target, type, listener) {
    target.addEventListener(type, function fn(event) {
        target.removeEventListener(type, fn);
        listener(event);
    });
}

function getRetargetIMCookieCB(callback) {

    var cookie;
    var allCookies = ';' + document.cookie;
    allCookies.split(';').forEach(function (c) {
        if (c.includes('RetargetIM')) {
            cookie = (c.split('=').pop());
        }
    });
    callback(cookie);
    //return parts.pop().split(";").shift();
}


function getWPSessionCookieCB(callback) {
    var allCookies = "; " + document.cookie;
    var value = null;
    if (allCookies.includes("wp_woocommerce_session")) {
        var wpwc = allCookies.split("wp_woocommerce_session");
        var sessionCookies = wpwc.pop();
        var cart = sessionCookies.split(";").shift();
        // gets the last  -  seesuin#=cookie;
        value = cart.split('=').pop();
        callback(value);
    }
    //return parts.pop().split(";").shift();
}

var fbID = null;
var cookie;


function ConfGetValue(json, family, key) {
    for (var i = 0; i < json[family].length; i++) {
        if (json[family][i].Id == key)
            return json[family][i].Value;
    }
}

function loadConf(callback) {
    var path = folderPath + '/clientConf.json';
    jQuery.getJSON(path, function (confJson) {
        //console.log(confJson);
        server = confJson.server + "/" + confJson.serverAdd;
        config = confJson.fcm;
        isPlanEnabled = confJson.plans.isEnabled;
        isEnabled = ConfGetValue(confJson, "settings", "Enabled");
        allSite = ConfGetValue(confJson, "settings", "AllSite");
        InvitationTextHeader = ConfGetValue(confJson, "settings", "invitationTextHeader");
        InvitationTextBody = ConfGetValue(confJson, "settings", "invitationTextBody");
        InvitationTextHeaderMobile = ConfGetValue(confJson, "settings", "invitationTextHeaderMobile");
        InvitationTextBodyMobile = ConfGetValue(confJson, "settings", "invitationTextBodyMobile");
        InvitationTextFooter = ConfGetValue(confJson, "settings", "invitationTextFooter");
        BackColor = ConfGetValue(confJson, "settings", "invitationColor");
        TextColor = ConfGetValue(confJson, "settings", "invitationTextColor");
        BorderColor = ConfGetValue(confJson, "settings", "invitationBorderColor");
        HeadColor = ConfGetValue(confJson, "settings", "invitationHeadColor");


        if (document.getElementById("njsScript") == null) {
            var njsPath = folderPath + "/notify.min.js"
            var serverPath = njsPath;

            var njs_src = document.createElement("script");
            njs_src.id = "njsScript";
            njs_src.src = serverPath;
            document.body.appendChild(njs_src);
        }
        document.getElementById("njsScript").onload = function (f) {
            addFCM(true);
            if (callback)
                callback();
        }

    });

}


function addFCM(onLoad) {
    if (document.getElementById("add_fcm") == null) {
        var fcm_src = document.createElement("script");
        fcm_src.id = "add_fcm";
        fcm_src.src = "https://www.gstatic.com/firebasejs/3.6.9/firebase.js";
        document.body.appendChild(fcm_src);
    }

    document.getElementById("add_fcm").onload = function (f) {
        //if(firebase==null)
        firebase.initializeApp(config);
		var isIncognito = (document.cookie.split('=').length <=2); //no cookies but us...
 
        FCM = firebase.messaging();
        if (FCM != null && isEnabled !== 0 && !isIncognito && isPlanEnabled !== 0) {
            var swPath = //'https://'+document.location.host+
                //'/wp-content/plugins/RetargetIM_wpp/src/RetargetIMNS/RetargetIM'
                folderPath +
                '/firebase-messaging-sw.js';
            navigator.serviceWorker.register(swPath)
                .then((registration) => {
                FCM.useServiceWorker(registration);
            console.log('fcm loaded sw');
            FCM.getToken().then(function (t) {
                if (t != null)
                    Token = t;
                //check token

                if (isShopPage() || allSite == 1) {
                    UpsertCust();

                    if (t == null) {
                        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                       var isFireFox = /Firefox/i.test(navigator.userAgent);
                        // var footerText = "Remove yourself at any time, no personal details required";
                        var myHtml;
                        /*    myHtml = "<div style='border-style:solid;border-color:" + BorderColor + ";padding:10px;border-radius: 10px;border-width:4px;'> <div style='text-align:right'><b>X</b></div>"
                         +
                         "<div style='background-color:" + BorderColor + ";color:" + BackColor + ";border-radius: 10px;text-align:center;font-weight: bold;padding:5px;font-size:x-large'>" +
                         InvitationTextHeader +
                         "</div>" +
                         "<span style='font-size:large' data-notify-text/>" +
                         "<div style='font-size:small'>"
                         + InvitationTextFooter +
                         "</div></div>";
                         */

                        var dir = 'left';
						var opDir = 'right';
						if (document.dir === 'rtl')
						{
                            dir = 'right';
							opDir = 'left';
						}
						var allowText = 'Allow';
                        var allowButtonStyle = "display:inline-block;background-color:" + TextColor + ";color:" + BackColor + ";box-shadow:0px 0px 2px 2px grey;padding:5px 50px;position:absolute;"+opDir +":25px;cursor:pointer";

                       
                        var xLoc = '-44px';
                        if (isMobile) {
                            xLoc = '-13%';
                            allowButtonStyle =
                                "background-color:" + TextColor +
                                ";color:" + BackColor +
                                ";text-align:center;width:100%;box-shadow:0px 0px 2px 2px grey;padding:5px 20px";
                        }
//TEST the notifyjs-clickable!

                        myHtml =
                            "<div style='border-style:solid;border-color:" + BorderColor + ";padding:25px;border-radius: 10px;border-width:6px;'> " +
                            "<div id='X' style='background-color:" + BackColor + ";text-align:center;font-size:x-large;width:40px;margin-" + dir + ":101%;margin-top:" + xLoc + ";border-style:solid;border-color:" + BorderColor + ";color:" + HeadColor + "'><b>X</b></div>" +
                            "<div style='color:" + HeadColor + ";text-align:center;font-weight: bold;padding-top:10px;font-size:150%;margin-top:-3%'>" +
                            InvitationTextHeader +
                            "</div>" +
                            "<span  data-notify-text/>" +
                            "<hr style='margin-bottom:3px'>" +

                            "<div class='notifyjs-clickable' style='display:inline-block;font-size:small;margin-top: -2%;margin-bottom: -1%;'>" +
                            "<div  style='display:inline-block;'>" +
                            InvitationTextFooter +
                            "</div> <div class ='allowButton' style='" + allowButtonStyle + "'>" +
                            allowText +
                            "</div>" +
                            "</div></div>";

                        var myCSSstyle =
                            {
                                html: myHtml,
                                classes: {
                                    base: {
                                        "position": "fixed",
                                        "background-color": BackColor,
                                        "color": TextColor,
                                        "border-color": BorderColor,
                                        "border-style": "solid",
                                        "border-width": "thin",
                                        "right": "0%",
                                        "left": "0%",
                                        "box-shadow": "0px 3px 30px black",
                                        "width": window.outerWidth * 0.95 + "px",
                                        "height": "auto",
                                        "padding-left": "5px",
                                        "padding-right": "5px",
                                        "z-index": "2000000",
                                        "top": "5%",
                                        "font-size": "large" //for regular text

                                    },
                                    desktop: {
                                        "min-height": "250px",
                                        "right": '25%',//((window.outerWidth / 2 - 275) + "px"),
                                        "left": '25%',//((window.outerWidth / 2 - 275) + "px"),
                                        "top": '25%',//"100px",//"15%",
                                        "width": "50%",
                                        "font-size": "xx-large" //for regular text

                                    },
                                    firefoxMobile: {
                                        "bottom": '10%'

                                    }
                                }
                            };

                        jQuery.notify.addStyle('RIMStyle', myCSSstyle);
                        setTimeout(function () { //},2000);  show pop up only after 2 seconds...
                            if (!sessionStorage.alreadyClicked) {
                                // sessionStorage.alreadyClicked = 1;
                                if (isMobile) //Mobile
                                {
                                    if (isFireFox)
                                        jQuery.notify(InvitationTextBody, {
                                            style: "RIMStyle",
                                            className: "firefoxMobile",
                                            autoHide: false,
                                            clickToHide: false,
                                            position: "top",
                                            gap: 10
                                        });
                                    else
                                        jQuery.notify(InvitationTextBodyMobile, {
                                            style: "RIMStyle",
                                            autoHide: false,
                                            clickToHide: false,
                                            position: "top",
                                            gap: 10
                                        });

                                }
                                else //Desktop
                                {
                                    jQuery.notify(InvitationTextBody, {
                                        style: "RIMStyle",
                                        className: "desktop",
                                        autoHide: false,
                                        clickToHide: false,
                                        position: "top",
                                        gap: 10
                                    });
                                }

                                jQuery(document).on('click', '.notifyjs-RIMStyle-base #X', function () {
                                    sessionStorage.alreadyClicked = 1;
                                    jQuery(".notifyjs-RIMStyle-base")[0].style.display = "none";

                                });
                                jQuery(document).on('click', '.notifyjs-RIMStyle-base .notifyjs-clickable', function () {
                                    //if(!sessionStorage.alreadyClicked)
                                    {
                                        // setTimeout(function () { //},500);   request 0.5 seconds after the pop up
                                        FCM.requestPermission()
                                            .then(function () {
                                                console.log('Notification permission granted.');
                                                jQuery.notify("Thank you for subscribing", "success");
                                                jQuery(".notifyjs-RIMStyle-base")[0].style.display = "none";
                                                return FCM.getToken();
                                            })
                                            .then(function (currentToken) {
                                                sessionStorage.alreadyClicked = 1;
                                                Token = currentToken;
                                                CustClicked();
                                                UpsertCust();
                                            })
                                            .catch(function (err) {
                                                console.log('Unable to get permission to notify.', err);
                                                jQuery(".notifyjs-RIMStyle-base")[0].style.display = "none";

                                            });

                                        //}, 1000);  //request 1 second after the pop up
                                        sessionStorage.alreadyClicked = 1;
                                    }
                                });
                            }


                        }, 2000); //show pop up only after 2 seconds...


                        //if clicked - don't show in the session
                        //	jQuery(document).on('click', '.notifyjs-RIMStyle-base', function() {
                        //programmatically trigger propogating hide event
                        //  $jQuery(this).trigger('notify-hide');


                    }
                }

            });


        })
            ;

            FCM.onMessage(function (payload) {
                console.log('onMessage: ', payload);
                //  alert(payload.notification.title);

            });

        }
    }
}


function FBMLoad(constFlag) {


    var messengermessageusdiv = "<div class=fb-messengermessageus " +
        "  messenger_app_id=290168538044939 " +
        "  page_id=154120788342419" +
        "  color=blue" +
        "  size=standard >" +
        "</div> ";
    //if
    //for(var counter=0;counter < 100000 && !document.cookie.includes(wpCookies);counter++)
    //  if(document.cookie.includes(wpCookies))
    {
        getRetargetIMCookieCB(function (cookie) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) { // `DONE`
                    status = xhr.status;
                    if (status == 200) {
                        //	alert('response:' + xhr.responseText);

                        var Serial = (JSON.parse(xhr.responseText)).serial;
                        if (!isNaN(Serial)) {

                            var SessionMessage = 'Have you heard about our new BOT? </br> Access your cart from anywhere! be the first to know on sales! and much more... </br> <a href="https://www.messenger.com/t/154120788342419?ref=' + Serial + '" target="_blank" ><b> Message us</b></a>';
                            var addition = '</br>If you want to re-login to the bot - you can always enter the code: ' + Serial;

                            var InvitationTextBody = 'Have you heard about our new BOT? </br> Access your cart from anywhere! \n Click here be the first to know on sales! and much more...';

                            InvitationTextBody = "if you love us, click here to get personal notifications and reminders on the products relevent for you!";

                            var msglink = "https://www.messenger.com/t/154120788342419?ref=" + Serial + "";
                            if (constFlag) {

                            }

                        }
                    }
                }
            }
            xhr.open('GET', server + '/serial' + '?cookie=' + cookie, true);
            xhr.send();
        });
    }

}

function isShopPage() {

    if (
        document.location.href.toLowerCase().includes("shop") ||
        document.location.href.toLowerCase().includes("store") ||
        document.location.href.toLowerCase().includes("category") ||
        document.location.href.toLowerCase().includes("product") ||
        document.location.href.includes("חנות") ||
        document.location.href.toLowerCase().includes("%d7%97%d7%a0%d7%95%d7%aa") ||
        document.location.href.includes("מוצר") ||
        document.location.href.toLowerCase().includes("%d7%9e%d7%95%d7%a6%d7%a8")

    //document.location.href.includes("cart")

    )
        return true;
    else
        return false;

}

function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }

    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
        s4() + '-' + s4() + s4() + s4();
}


