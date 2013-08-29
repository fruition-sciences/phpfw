/**
 * Event for clicking on a button. Opens the given URL.
 *
 * @param url
 * @param target (optional) if set, URL will be opened in a new window with this
 *        name.
 */
function button_click(url, target) {
    url = normalizeUrl(url);
    if (target) {
        window.open(url, target);
    } else {
        document.location = url;
    }
}

/**
 * Event for clicking on a button. Supmits the form.
 *
 * @param url
 * @param target (optional) if set, form will be submitted into a new window
 *        with this name.
 */
function button_submit(url, target) {
    var form = document.getElementById("theForm");
    form.method = "post";
    url = normalizeUrl(url);
    form.action = url;
    if (target) {
        form.target = target;
    }
    form.submit();
}

function normalizeUrl(url) {
    if (url.charAt(0) == '/') {
      return appBase + url.substring(1);
    } else {
        return url;
    }
}

/**
 * Focus on the first form element which is visible and which is not a button
 * and not a dateBox.
 * Prevent scrolling to the bottom of the page when focus is on an element not at the top of the page.
 */
function focusOnFirst() {
    var x = window.scrollX, y = window.scrollY;
    jQuery(':input:visible:not(button):not(".date,.dateTime"):first').focus();
    window.scrollTo(x, y);
}

/**
 * Array listing selectors. If an input element match on of these selectors and
 * if the enter key is pressed while this input is focused, then the
 * ui_pressDefaultButton function will be aborted.
 * @var Array
 */
var defaultButtonIgnore = [];
/**
 * Press the default (or most appropriate) button.
 *
 * @param e jQuery event
 */
function ui_pressDefaultButton(e) {
    var input = jQuery(e.target),
        parents = input.parents('form, div, fieldset');
    $.each(defaultButtonIgnore, function(index, value) {
        if (input.is(value)) {
            return;
        }
    });
    parents.each(function(i, element) {
        // Find first button (or element with attribute button="1")
        // TODO: We may want to give priority to primary button
        var button = jQuery(element).find('button, [button="1"]').first();
        if (button.length == 0) {
            return true; // continue
        }
        jQuery(button).click();
        return false; // break
    });
}

/**
 * Function use by the DateBox field when it it a time only field.
 * Update the hidden field with the data from the dropdowns.
 * @param fieldName
 * @return
 */
function updateHiddenTimeField(fieldName) {
    document.getElementById(fieldName).value = document.getElementById("_" + fieldName + "_hour").value + ":" + document.getElementById("_" + fieldName + "_minute").value + " " + document.getElementById("_" + fieldName + "_pm").value;
}

/**
 * Function use by the DateBox field when it is a time only field.
 * Update the dropdown fields with the data from the hidden field.
 * @param fieldName
 * @return
 */
function updateSelectTimeFields(fieldName) {
    if (document.getElementById(fieldName)) {
        var time = document.getElementById(fieldName).value;
        if (time == "") {
            updateHiddenTimeField(fieldName);
        } else {
            var temp = [],
                temp2 = [];
            temp = time.split(":");
            temp2 = temp[1].split(" ");
            if (temp2[0] < 8) {
                temp2[0] = "00";
            } else if (temp2[0] < 23) {
                temp2[0] = "15";
            } else if (temp2[0] < 38) {
                temp2[0] = "30";
            } else {
                temp2[0] = "45";
            }
            document.getElementById("_" + fieldName + "_hour").value = temp[0];
            document.getElementById("_" + fieldName + "_minute").value = temp2[0];
            document.getElementById("_" + fieldName + "_pm").value = temp2[1];
        }
    }
}

/**
 * Removes blank spaces in front and at the end of the string
 *
 * @param String
 */
function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g, "");
}

function moveIt(obj, mvTop, mvLeft) {
    obj.style.position = "absolute";
    obj.style.top = mvTop + 'px';
    obj.style.left = mvLeft + 'px';
}


var xMousePos = 0,
    yMousePos = 0;

function captureMousePosition(e) {
    var posx = 0, posy = 0;
    if (!e) {
        e = window.event;
    }
    if (e.pageX || e.pageY) {
        posx = e.pageX;
        posy = e.pageY;
    } else if (e.clientX || e.clientY) {
        posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    // posx and posy contain the mouse position relative to the document
    xMousePos = posx;
    yMousePos = posy;
}
function encode(value) {
    value = value.replace(/\r\n/g, "\n");
    var utftext = "";
    for (var n = 0; n < value.length; n++) {
        var c = value.charCodeAt(n);
        if (c < 128) {
            utftext += String.fromCharCode(c);
        } else if ((c > 127) && (c < 2048)) {
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        } else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }
    }
    return escape(utftext);
}

/**
 * This function return a date object after accepting
 * a date string and a dateseparator as arguments
 * @param dateString (MM/DD/YY)
 * @param dateSeperator (/)
 * @return Date object
 */
function getDateObject(dateString, dateSeperator) {
    var curValue = dateString,
    sepChar = dateSeperator,
    curPos = 0,
    cDate,cMonth,cYear;

    //extract month portion
    curPos = dateString.indexOf(sepChar);
    cMonth = dateString.substring(0, curPos);

    //extract day portion
    endPos = dateString.indexOf(sepChar,curPos+1);
    cDate = dateString.substring(curPos+1,endPos);

    //extract year portion
    curPos = endPos;
    endPos = curPos + 3;
    cYear=curValue.substring(curPos + 1, endPos);
    if (cYear.length == 2) {
        cYear = "20" + cYear;
    }
    //Create Date Object
    dtObject=new Date(cYear, cMonth-1, cDate);
    return dtObject;
}