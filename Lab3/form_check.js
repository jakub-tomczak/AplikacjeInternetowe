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
    if (isEmpty(form.elements["f_imie"].value)) {
        alert("Podaj imię!")
        return false
    }
    return true
}

function checkString(str, msg) {
    if (isEmpty(str)) {
        alert(msg)
        return false
    }
    return true
}

function checkZIP(zip, msg) {
    if (zip.length == 6) {
        zip.replace('-', '');
    }
    if (isEmpty(zip) || (zip.length != 5 || zip.length != 6)) {
        alert(msg)
        return false
    }

    for (var i in zip) {

    }
}
