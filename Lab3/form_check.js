// JavaScript source code
function isWhiteSpace(str) {
    var ws = "\t\n\r ";
    for (var i = 0; i < str.length; i++) {
        var c = str.charAt(i)
        if (ws.indexOf(c) == -1) {
            return false
        }
    }
    return true
}
function isEmpty(str) {
    return str.length == 0 || isWhiteSpace(str);
}

function validate(form) {
    var value = true
    for (i = 0; i < form.length; i++) {
        if (form[i].className.indexOf('string') >= 0) {
            if(checkString(form.elements[], "Podaj imię!"))
        } else if (form[i].className.indexOf('email') >= 0) {

        } else if (form[i].className.indexOf('zip_code') >= 0) {

        }
    }

    value &= checkString(form.elements["f_imie"], "Podaj imię!")
    value &= checkString(form.elements["f_nazwisko"], "Podaj nazwisko!")
    value &= checkString(form.elements["f_ulica"], "Podaj ulicę!")
    value &= checkString(form.elements["f_miasto"], "Podaj miasto!")

    value &= checkZIP(form.elements["f_kod"].value, "Podaj kod pocztowy!")

    value &= checkEmailRegEx(form.elements["f_email"].value)

    return value
}

function checkString(obj, msg) {
    if (isEmpty(obj.value)) {
        checkStringAndFocus(obj, msg)
        return false
    }
    return true
}

function checkZIP(zip, msg) {
    if (zip.length == 6) {
        zip = zip.replace('-', '');
    }
    if (isEmpty(zip) || (zip.length != 5)) {
        alert(msg)
        return false
    }

    for (var i in zip) {
        if (isNaN(Number(i))) {
            alert(msg)
            return false
        }
    }
    return true
}

function checkEmail(str) {
    if (isWhiteSpace(str)) {
        alert("Podaj właściwy e-mail");
        return false;
    }
    else {
        var at = str.indexOf("@");
        if (at < 1) {
            alert("Nieprawidłowy e-mail");
            return false;
        }
        else {
            var l = -1;
            for (var i = 0; i < str.length; i++) {
                var c = str.charAt(i);
                if (c == ".") {
                    l = i;
                }
            }
            if ((l < (at + 2)) || (l == str.length - 1)) {
                alert("Nieprawidłowy e-mail");
                return false;
            }
        }
        return true;
    }
}

function checkEmailRegEx(str) {
    var email = /[a-zA-Z_0-9\.]+@[a-zA-Z_0-9\.]+\.[a-zA-Z][a-zA-Z]+/;
    if (email.test(str))
        return true;
    else {
        alert("Podaj właściwy e-mail");
        return false;
    }
}

function checkStringAndFocus(obj, msg) {
    var str = obj.value;
    var errorFieldName = "e_" + obj.name.substr(2, obj.name.length);
    if (isEmpty(str)) {
        document.getElementById(errorFieldName).innerHTML = msg;
        startTimer(errorFieldName)
        obj.focus();
        return false;
    }
    else {
        return true;
    }
}

var errorField = "";
function startTimer(fName) {
    errorField = fName;
    window.setTimeout("clearError(errorField)", 5000);
}
function clearError(objName) {
    document.getElementById(objName).innerHTML = "";
}

function showElement(e) {
    document.getElementById(e).style.visibility = 'visible';
}
function hideElement(e) {
    document.getElementById(e).style.visibility = 'hidden';
}

function checkZIPCodeRegEx(obj, msg_field) {
    var zip = /^\d{2}-\d{3}$/
    field = document.getElementById(msg_field)
    if (!zip.test(obj.value)) {
        field.innerHTML = "Podaj poprawny kod pocztowy"
        field.className = "code_false"
        return false
    } else {
        field.innerHTML = "OK"
        field.className = "code_true"
        return true
    }
}

function alterRows(i, e) {
    if (e) {
        if (i % 2 == 1) {
            e.setAttribute("style", "background-color: Aqua;");
        }
        e = e.nextSibling;
        while (e && e.nodeType != 1) {
            e = e.nextSibling;
        }
        alterRows(++i, e);
    }
}

function documentReady() {
    b = document.getElementsByClassName('colored_row')

    for (i = 0; i < b.length; i++) {
        alterRows(1, b[i]);
    }


}
window.onload = documentReady


function nextNode(e) {
    while (e && e.nodeType != 1) {
        e = e.nextSibling;
    }
    return e;
}
function prevNode(e) {
    while (e && e.nodeType != 1) {
        e = e.previousSibling;
    }
    return e;
}
function swapRows(b) {
    var q = 1
    var tab = prevNode(b.previousSibling);
    var tBody = nextNode(tab.firstChild);
    var lastNode = prevNode(tBody.lastChild);
    tBody.removeChild(lastNode);
    var firstNode = nextNode(tBody.firstChild);
    tBody.insertBefore(lastNode, firstNode);
}

function cnt(form, msg, maxSize) {
    if (form.value.length > maxSize)
        form.value = form.value.substring(0, maxSize);

    msg.innerHTML = maxSize - form.value.length;
}