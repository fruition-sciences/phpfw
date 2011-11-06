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
    }
    else {
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
    }
    else return url;
}

function focusOnFirst() {
    var elements = document.getElementsByTagName('input');
    for (var i=0; i<elements.length; i++) {
        var elem = elements[i];
        if (elem.type == 'text') {
            try {
                elem.focus();
            }
            catch (e) {
                // IE refuses to focus on hidden elements
            }
            return;
        }
    }
}

function formKeyPress(e) {
    var key = null;
    var control = null;
    if (e.target) { // FireFox
        key = e.which;
        control = e.target;
    }
    else { //IE
        key = window.event.keyCode;
        control = e.srcElement;
    }
    //alert(control.nodeName);
    //alert(control.type);
    if (control.nodeName == 'SELECT' || control.nodeName == 'INPUT' && (control.type == 'text' || control.type == 'password' || control.type == 'checkbox' || control.type == 'radio')) {
        if (key == 13 || key == 3) {
            var button = getFirstButton(control);
            if (button) {
                button.onclick();
            }
            return false;
        }
    }
    return true;
}

/**
 * Get the first button in the same container as the given element.
 * A container, in this context, is either a form or an element that has the
 * 'group' attribute.
 * 
 * @param HTMLElement element
 * @return the first button, or null if no button was found.
 */
function getFirstButton(element) {
    var container = findContainer(element);
    if (!container) {
        return null;
    }
    return getFirstButtonChild(container);
}

/**
 * Find the first child button of the given element.
 * 
 * @param HTMLElement element
 * @return the first button.
 */
function getFirstButtonChild(element) {
    var nodes = element.getElementsByTagName('button');
    if (nodes.length > 0) {
        return nodes[0];
    }
    // Look for 'span' with 'button' attribute.
    var nodes = element.getElementsByTagName('span');
    for (var i=0; i<nodes.length; i++) {
        var node = nodes.item(i);
        if (node.getAttribute('button')) {
            return node;
        }
    }
    // Same with 'img' tag. TODO: Change to look for any type.
    var nodes = element.getElementsByTagName('img');
    for (var i=0; i<nodes.length; i++) {
        var node = nodes.item(i);
        if (node.getAttribute('button')) {
            return node;
        }
    }
    return null;
}

/**
 * Find the container of the given element. A container, in this context, is
 * either a form or an element that has the 'group' attribute.
 * 
 * @param HTMLElement element
 * @return the container of the given element.
 */
function findContainer(element) {
    while ((element = element.parentNode) != null) {
        if (element.nodeName == 'FORM') {
            return element;
        }
    }
    return null;
}

/**
 * Function use by the DateBox field when it it a time only field.
 * Update the hidden field with the data from the dropdowns.
 * @param fieldName
 * @return
 */
function updateHiddenTimeField(fieldName){
    document.getElementById(fieldName).value = document.getElementById("_"+fieldName+"_hour").value +":"+ document.getElementById("_"+fieldName+"_minute").value +" "+ document.getElementById("_"+fieldName+"_pm").value;
}

/**
 * Function use by the DateBox field when it is a time only field.
 * Update the dropdown fields with the data from the hidden field.
 * @param fieldName
 * @return
 */
function updateSelectTimeFields(fieldName){
    if(document.getElementById(fieldName)){
        var time = document.getElementById(fieldName).value;
        if(time == ""){
            updateHiddenTimeField(fieldName);
        }else{
            var temp = new Array();
            var temp2 = new Array();
            temp = time.split(":");
            temp2 = temp[1].split(" ");
            if(temp2[0]<8){
                temp2[0]="00";
            }else if(temp2[0]<23){
                temp2[0]="15";
            }else if(temp2[0]<38){
                temp2[0]="30";
            }else{
                temp2[0]="45";
            }
            document.getElementById("_"+fieldName+"_hour").value = temp[0];
            document.getElementById("_"+fieldName+"_minute").value = temp2[0];
            document.getElementById("_"+fieldName+"_pm").value= temp2[1];
        }
    }
}

/**
 * Removes blank spaces in front and at the end of the string
 * 
 * @param String
 */
function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,"");
}

/**
 * Hides an element by id
 * 
 * @param ID
 */
function hideElement(id) {
    if (document.getElementById) { // DOM3 = IE5, NS6
        document.getElementById(id).style.display = 'none';
    } else {
        if (document.layers) { // Netscape 4
            eval("document." + id + ".visibility = 'hidden'");
        } else { // IE 4
            eval("document.all." + id + ".style.visibility = 'hidden'");
        }
    }
}

/**
 * Shows an element by id
 * 
 * @param ID
 */
function showElement(id) {
    if (document.getElementById) { // DOM3 = IE5, NS6
        document.getElementById(id).style.display = 'block';
    } else {
        if (document.layers) { // Netscape 4
            eval("document." + id + ".visibility = 'visible'");
        } else { // IE 4
            eval("document.all." + id + ".style.visibility = 'visible'");
        }
    }
} 

function moveIt(obj, mvTop, mvLeft) {
    obj.style.position = "absolute";
    obj.style.top = mvTop + 'px';
    obj.style.left = mvLeft + 'px';
}


var xMousePos = 0;
var yMousePos = 0;

function captureMousePosition(e) {
    var posx = 0;
    var posy = 0;
    if (!e) var e = window.event;
    if (e.pageX || e.pageY)     {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY)    {
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
    value = value.replace(/\r\n/g,"\n");
    var utftext = "";
  for (var n = 0; n < value.length; n++) {
    var c = value.charCodeAt(n);
    if (c < 128) { utftext += String.fromCharCode(c); }
    else if((c > 127) && (c < 2048)) {
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
function getDateObject(dateString,dateSeperator){
    var curValue=dateString;
    var sepChar=dateSeperator;
    var curPos=0;
    var cDate,cMonth,cYear;

    //extract month portion
    curPos=dateString.indexOf(sepChar);
    cMonth=dateString.substring(0,curPos);
    
    //extract day portion               
    endPos=dateString.indexOf(sepChar,curPos+1);            
    cDate=dateString.substring(curPos+1,endPos);

    //extract year portion              
    curPos=endPos;
    endPos=curPos+3;            
    cYear=curValue.substring(curPos+1,endPos);
    if(cYear.length == 2){
        cYear = "20" + cYear;
    }
    //Create Date Object
    dtObject=new Date(cYear,cMonth-1,cDate);    
    return dtObject;
}