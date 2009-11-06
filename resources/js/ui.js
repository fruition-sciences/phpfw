function button_click(url) {
    url = normalizeUrl(url);
    document.location = url;
}

function button_submit(url) {
    var form = document.getElementById("theForm");
    form.method = "post";
    url = normalizeUrl(url);
    form.action = url;
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
//    alert(control.nodeName);
//    alert(control.type);
    if (control.nodeName == 'INPUT' && (control.type == 'text' || control.type == 'password')) {
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
